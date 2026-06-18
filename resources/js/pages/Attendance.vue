<script setup lang="ts">
import AttendanceApprovalPanel from '@/components/attendance/AttendanceApprovalPanel.vue';
import AttendanceAuditHistory from '@/components/attendance/AttendanceAuditHistory.vue';
import AttendanceEditForm from '@/components/attendance/AttendanceEditForm.vue';
import AttendanceRecordsTable from '@/components/attendance/AttendanceRecordsTable.vue';
import TodayAttendanceCard from '@/components/attendance/TodayAttendanceCard.vue';
import type { AttendanceRecord } from '@/types/attendance';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps<{
    records: AttendanceRecord[];
    todayRecord: AttendanceRecord | null;
    canApprove: boolean;
}>();

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Attendance',
                href: '/attendance',
            },
        ],
    },
});

const selectedRecord = ref<AttendanceRecord | null>(props.todayRecord);

const editForm = useForm({
    check_in_time: props.todayRecord?.check_in_time?.slice(0, 5) ?? '',
    check_out_time: props.todayRecord?.check_out_time?.slice(0, 5) ?? '',
    reason_for_change: '',
});

const approvalForm = useForm({
    reason_for_change: '',
});

const selectRecord = (record: AttendanceRecord) => {
    selectedRecord.value = record;
    editForm.clearErrors();
    editForm.check_in_time = record.check_in_time?.slice(0, 5) ?? '';
    editForm.check_out_time = record.check_out_time?.slice(0, 5) ?? '';
    editForm.reason_for_change = '';
    approvalForm.clearErrors();
    approvalForm.reason_for_change = '';
};

const checkIn = () => {
    router.post('/attendance/check-in', {}, { preserveScroll: true });
};

const checkOut = () => {
    router.post('/attendance/check-out', {}, { preserveScroll: true });
};

const updateAttendance = () => {
    if (!selectedRecord.value) {
        return;
    }

    editForm.put(`/attendance/${selectedRecord.value.id}`, {
        preserveScroll: true,
        onSuccess: () => editForm.reset('reason_for_change'),
    });
};

const approveAttendance = () => {
    if (!selectedRecord.value) {
        return;
    }

    approvalForm.post(`/attendance/${selectedRecord.value.id}/approve`, {
        preserveScroll: true,
        onSuccess: () => approvalForm.reset('reason_for_change'),
    });
};

const rejectAttendance = () => {
    if (!selectedRecord.value) {
        return;
    }

    approvalForm.post(`/attendance/${selectedRecord.value.id}/reject`, {
        preserveScroll: true,
        onSuccess: () => approvalForm.reset('reason_for_change'),
    });
};
</script>

<template>
    <Head title="Attendance" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
        <div class="grid gap-4 lg:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-4">
                <TodayAttendanceCard
                    :today-record="todayRecord"
                    @check-in="checkIn"
                    @check-out="checkOut"
                />

                <AttendanceRecordsTable
                    :records="records"
                    :selected-record="selectedRecord"
                    @select="selectRecord"
                />

                <AttendanceAuditHistory :selected-record="selectedRecord" />
            </div>

            <div class="space-y-4">
                <AttendanceEditForm
                    :selected-record="selectedRecord"
                    :form="editForm"
                    @submit="updateAttendance"
                />

                <AttendanceApprovalPanel
                    v-if="canApprove"
                    :selected-record="selectedRecord"
                    :form="approvalForm"
                    @approve="approveAttendance"
                    @reject="rejectAttendance"
                />
            </div>
        </div>
    </div>
</template>
