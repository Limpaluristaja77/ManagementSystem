<?php

namespace App\Console\Commands;

use App\Models\AttendanceRecord;
use App\Models\User;
use App\Notifications\IncompleteAttendanceNotification;
use Illuminate\Console\Command;

class SendMissingAttendanceReminders extends Command
{
    protected $signature = 'attendance:send-missing-reminders {type : check-in or check-out}';

    protected $description = 'Send attendance reminder emails for missing daily check-in or check-out.';

    public function handle(): int
    {
        $type = $this->argument('type');

        if (! in_array($type, ['check-in', 'check-out'], true)) {
            $this->error('Type must be either check-in or check-out.');

            return self::FAILURE;
        }

        $sent = 0;

        User::query()
            ->whereNull('deactivated_at')
            ->get()
            ->filter(fn (User $user) => $user->can('attendance.create') && ! $user->hasRole('superadmin'))
            ->each(function (User $user) use ($type, &$sent): void {
                $record = AttendanceRecord::query()
                    ->where('user_id', $user->id)
                    ->whereDate('work_date', today())
                    ->first();

                if (! $this->shouldNotify($type, $record)) {
                    return;
                }

                $user->notify(new IncompleteAttendanceNotification($type));
                $sent++;
            });

        $this->info("Sent {$sent} {$type} reminder(s).");

        return self::SUCCESS;
    }

    private function shouldNotify(string $type, ?AttendanceRecord $record): bool
    {
        if ($type === 'check-in') {
            return $record === null || $record->check_in_time === null;
        }

        return $record !== null
            && $record->check_in_time !== null
            && $record->check_out_time === null;
    }
}
