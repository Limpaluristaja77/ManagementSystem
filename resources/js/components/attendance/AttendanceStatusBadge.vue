<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import type { AttendanceStatus } from '@/types/attendance';

defineProps<{
    status: AttendanceStatus | 'not started';
}>();

const statusVariant = (status: AttendanceStatus | 'not started') => {
    if (status === 'approved') {
        return 'default';
    }

    if (status === 'rejected') {
        return 'destructive';
    }

    return 'secondary';
};
</script>

<template>
    <Badge :variant="statusVariant(status)">
        {{ status }}
    </Badge>
</template>
