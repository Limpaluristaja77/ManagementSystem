<?php

namespace App\Http\Controllers;

use App\Models\AttendanceAudit;
use App\Models\AttendanceRecord;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class AttendanceRecordController extends Controller
{
    public function index(Request $request): Response
    {
        $user = $request->user();
        $canApprove = $user->can('attendance.approve');

        $records = AttendanceRecord::query()
            ->with([
                'user:id,name,email',
                'approvedBy:id,name,email',
                'audits' => fn ($query) => $query->with('changedBy:id,name,email')->latest(),
            ])
            ->when(! $canApprove, fn ($query) => $query->where('user_id', $user->id))
            ->latest('work_date')
            ->latest()
            ->take(50)
            ->get();

        $records->each(function (AttendanceRecord $record): void {
            $record->audits->each(function (AttendanceAudit $audit): void {
                $audit->setAttribute('changed_by_user', $audit->changedBy?->only(['id', 'name', 'email']));
            });
        });

        $todayRecord = AttendanceRecord::query()
            ->where('user_id', $user->id)
            ->whereDate('work_date', today())
            ->first();

        return Inertia::render('Attendance', [
            'records' => $records,
            'todayRecord' => $todayRecord,
            'canApprove' => $canApprove,
        ]);
    }

    public function checkIn(Request $request): RedirectResponse
    {
        $request->user()->can('attendance.create') || abort(403);

        $record = AttendanceRecord::firstOrCreate(
            [
                'user_id' => $request->user()->id,
                'work_date' => today()->toDateString(),
            ],
            [
                'check_in_time' => now()->format('H:i:s'),
                'status' => 'draft',
            ],
        );

        if ($record->wasRecentlyCreated) {
            Inertia::flash('toast', ['type' => 'success', 'message' => __('Checked in.')]);

            return to_route('attendance.index');
        }

        if ($record->status === 'approved') {
            abort(403);
        }

        if ($record->check_in_time === null) {
            $record->update(['check_in_time' => now()->format('H:i:s')]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Checked in.')]);

        return to_route('attendance.index');
    }

    public function checkOut(Request $request): RedirectResponse
    {
        $record = AttendanceRecord::query()
            ->where('user_id', $request->user()->id)
            ->whereDate('work_date', today())
            ->firstOrFail();

        if ($record->status === 'approved') {
            abort(403);
        }

        $record->update([
            'check_out_time' => now()->format('H:i:s'),
            'status' => 'submitted',
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Checked out.')]);

        return to_route('attendance.index');
    }

    public function update(Request $request, AttendanceRecord $attendanceRecord): RedirectResponse
    {
        $user = $request->user();

        if ($attendanceRecord->user_id !== $user->id && ! $user->can('attendance.update-approved')) {
            abort(403);
        }

        if ($attendanceRecord->status === 'approved' && ! $user->can('attendance.update-approved')) {
            abort(403);
        }

        $validated = $request->validate([
            'check_in_time' => ['nullable', 'date_format:H:i'],
            'check_out_time' => ['nullable', 'date_format:H:i'],
            'reason_for_change' => ['required', 'string', 'max:2000'],
        ]);

        $oldValues = $attendanceRecord->only(['check_in_time', 'check_out_time', 'status']);

        $attendanceRecord->update([
            'check_in_time' => $validated['check_in_time'],
            'check_out_time' => $validated['check_out_time'],
        ]);

        AttendanceAudit::create([
            'attendance_record_id' => $attendanceRecord->id,
            'changed_by' => $user->id,
            'action' => 'updated',
            'old_values' => $oldValues,
            'new_values' => $attendanceRecord->fresh()->only(['check_in_time', 'check_out_time', 'status']),
            'reason_for_change' => $validated['reason_for_change'],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attendance updated.')]);

        return to_route('attendance.index');
    }

    public function approve(Request $request, AttendanceRecord $attendanceRecord): RedirectResponse
    {
        $request->user()->can('attendance.approve') || abort(403);

        if ($attendanceRecord->status === 'approved') {
            return back()->withErrors([
                'reason_for_change' => __('This attendance record is already approved.'),
            ]);
        }

        $validated = $request->validate([
            'reason_for_change' => ['required', 'string', 'max:2000'],
        ]);

        $oldValues = $attendanceRecord->only(['status', 'approved_by', 'approved_at']);

        $attendanceRecord->update([
            'status' => 'approved',
            'approved_by' => $request->user()->id,
            'approved_at' => now(),
        ]);

        AttendanceAudit::create([
            'attendance_record_id' => $attendanceRecord->id,
            'changed_by' => $request->user()->id,
            'action' => 'approved',
            'old_values' => $oldValues,
            'new_values' => $attendanceRecord->fresh()->only(['status', 'approved_by', 'approved_at']),
            'reason_for_change' => $validated['reason_for_change'],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attendance approved.')]);

        return to_route('attendance.index');
    }

    public function reject(Request $request, AttendanceRecord $attendanceRecord): RedirectResponse
    {
        $request->user()->can('attendance.approve') || abort(403);

        $validated = $request->validate([
            'reason_for_change' => ['required', 'string', 'max:2000'],
        ]);

        $oldValues = $attendanceRecord->only(['status']);

        $attendanceRecord->update([
            'status' => 'rejected',
        ]);

        AttendanceAudit::create([
            'attendance_record_id' => $attendanceRecord->id,
            'changed_by' => $request->user()->id,
            'action' => 'rejected',
            'old_values' => $oldValues,
            'new_values' => $attendanceRecord->fresh()->only(['status']),
            'reason_for_change' => $validated['reason_for_change'],
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Attendance rejected.')]);

        return to_route('attendance.index');
    }
}
