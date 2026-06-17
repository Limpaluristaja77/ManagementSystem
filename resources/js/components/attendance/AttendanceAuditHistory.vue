<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import type { AttendanceAudit, AttendanceRecord } from '@/types/attendance';

defineProps<{
    selectedRecord: AttendanceRecord | null;
}>();

const formatJson = (value: AttendanceAudit['old_values']) => {
    if (!value) {
        return '-';
    }

    return JSON.stringify(value, null, 2);
};
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Audit history</CardTitle>
            <CardDescription>
                Changes saved for the selected attendance record.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <div v-if="!selectedRecord" class="py-8 text-center text-sm text-muted-foreground">
                Select a record to view audit history.
            </div>

            <div
                v-else-if="!selectedRecord.audits || selectedRecord.audits.length === 0"
                class="py-8 text-center text-sm text-muted-foreground"
            >
                No audit entries for this record yet.
            </div>

            <div v-else class="space-y-3">
                <div
                    v-for="audit in selectedRecord.audits"
                    :key="audit.id"
                    class="rounded-md border p-3"
                >
                    <div class="flex flex-wrap items-start justify-between gap-2">
                        <div class="space-y-1">
                            <Badge variant="outline">{{ audit.action }}</Badge>
                            <p class="text-sm text-muted-foreground">
                                {{ audit.changed_by_user?.name ?? 'Unknown user' }}
                                ·
                                {{ audit.created_at }}
                            </p>
                        </div>
                    </div>

                    <p class="mt-3 text-sm">
                        {{ audit.reason_for_change }}
                    </p>

                    <div class="mt-3 grid gap-3 text-xs md:grid-cols-2">
                        <div>
                            <div class="mb-1 font-medium">Old values</div>
                            <pre class="max-h-40 overflow-auto rounded bg-muted p-2">{{ formatJson(audit.old_values) }}</pre>
                        </div>
                        <div>
                            <div class="mb-1 font-medium">New values</div>
                            <pre class="max-h-40 overflow-auto rounded bg-muted p-2">{{ formatJson(audit.new_values) }}</pre>
                        </div>
                    </div>
                </div>
            </div>
        </CardContent>
    </Card>
</template>
