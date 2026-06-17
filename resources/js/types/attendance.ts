export type AttendanceUser = {
    id: number;
    name: string;
    email: string;
};

export type AttendanceStatus = 'draft' | 'submitted' | 'approved' | 'rejected';

export type AttendanceAudit = {
    id: number;
    attendance_record_id: number;
    changed_by: number;
    action: string;
    old_values: Record<string, unknown> | null;
    new_values: Record<string, unknown> | null;
    reason_for_change: string;
    created_at: string;
    updated_at: string;
    changed_by_user?: AttendanceUser;
};

export type AttendanceRecord = {
    id: number;
    user_id: number;
    work_date: string;
    check_in_time: string | null;
    check_out_time: string | null;
    status: AttendanceStatus;
    approved_at: string | null;
    manager_note: string | null;
    user?: AttendanceUser;
    approved_by?: AttendanceUser | null;
    audits?: AttendanceAudit[];
};
