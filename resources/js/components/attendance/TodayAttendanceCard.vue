<script setup lang="ts">
import AttendanceStatusBadge from '@/components/attendance/AttendanceStatusBadge.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { AttendanceRecord } from '@/types/attendance';
import { LogIn, LogOut } from '@lucide/vue';
import { computed } from 'vue';

const props = defineProps<{
    todayRecord: AttendanceRecord | null;
}>();

const emit = defineEmits<{
    checkIn: [];
    checkOut: [];
}>();

const todayStatus = computed(() => props.todayRecord?.status ?? 'not started');
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Attendance</CardTitle>
            <CardDescription>
                Record daily office arrival and departure times.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div class="flex flex-wrap items-center gap-3">
                <AttendanceStatusBadge :status="todayStatus" />

                <Button
                    type="button"
                    :disabled="todayRecord?.check_in_time != null"
                    @click="emit('checkIn')"
                >
                    <LogIn />
                    Check in
                </Button>

                <Button
                    type="button"
                    variant="secondary"
                    :disabled="
                        !todayRecord?.check_in_time ||
                        todayRecord?.check_out_time !== null ||
                        todayRecord?.status === 'approved'
                    "
                    @click="emit('checkOut')"
                >
                    <LogOut />
                    Check out
                </Button>
            </div>
        </CardContent>
    </Card>
</template>
