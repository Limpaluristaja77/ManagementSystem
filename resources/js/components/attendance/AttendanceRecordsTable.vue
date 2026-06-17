<script setup lang="ts">
import AttendanceStatusBadge from '@/components/attendance/AttendanceStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import type { AttendanceRecord } from '@/types/attendance';

defineProps<{
    records: AttendanceRecord[];
    selectedRecord: AttendanceRecord | null;
}>();

const emit = defineEmits<{
    select: [record: AttendanceRecord];
}>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Records</CardTitle>
        </CardHeader>
        <CardContent>
            <div class="overflow-hidden rounded-md border">
                <table class="w-full text-sm">
                    <thead class="bg-muted/60 text-left">
                        <tr>
                            <th class="px-3 py-2 font-medium">Date</th>
                            <th class="px-3 py-2 font-medium">User</th>
                            <th class="px-3 py-2 font-medium">In</th>
                            <th class="px-3 py-2 font-medium">Out</th>
                            <th class="px-3 py-2 font-medium">Status</th>
                            <th class="px-3 py-2 font-medium"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr
                            v-for="record in records"
                            :key="record.id"
                            class="border-t"
                            :class="{
                                'bg-muted/40': selectedRecord?.id === record.id,
                            }"
                        >
                            <td class="px-3 py-2">{{ record.work_date }}</td>
                            <td class="px-3 py-2">
                                {{ record.user?.name ?? 'Me' }}
                            </td>
                            <td class="px-3 py-2">
                                {{ record.check_in_time ?? '-' }}
                            </td>
                            <td class="px-3 py-2">
                                {{ record.check_out_time ?? '-' }}
                            </td>
                            <td class="px-3 py-2">
                                <AttendanceStatusBadge :status="record.status" />
                            </td>
                            <td class="px-3 py-2 text-right">
                                <Button
                                    type="button"
                                    size="sm"
                                    variant="outline"
                                    @click="emit('select', record)"
                                >
                                    Select
                                </Button>
                            </td>
                        </tr>
                        <tr v-if="records.length === 0">
                            <td
                                class="px-3 py-8 text-center text-muted-foreground"
                                colspan="6"
                            >
                                No attendance records yet.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </CardContent>
    </Card>
</template>
