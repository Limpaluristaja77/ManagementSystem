<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Label } from '@/components/ui/label';
import type { AttendanceRecord } from '@/types/attendance';
import type { InertiaForm } from '@inertiajs/vue3';
import { Check, X } from '@lucide/vue';

defineProps<{
    selectedRecord: AttendanceRecord | null;
    form: InertiaForm<{
        reason_for_change: string;
    }>;
}>();

const emit = defineEmits<{
    approve: [];
    reject: [];
}>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle>Manager approval</CardTitle>
            <CardDescription>
                Approval locks the record for normal users.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <form class="space-y-4">
                <div class="grid gap-2">
                    <Label for="approval_reason">Reason</Label>
                    <textarea
                        id="approval_reason"
                        v-model="form.reason_for_change"
                        class="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none transition-[color,box-shadow] focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!selectedRecord"
                    />
                    <InputError :message="form.errors.reason_for_change" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <Button
                        type="button"
                        :disabled="
                            !selectedRecord ||
                            selectedRecord.status === 'approved' ||
                            form.processing
                        "
                        @click="emit('approve')"
                    >
                        <Check />
                        Approve
                    </Button>
                    <Button
                        type="button"
                        variant="destructive"
                        :disabled="!selectedRecord || form.processing"
                        @click="emit('reject')"
                    >
                        <X />
                        Reject
                    </Button>
                </div>
            </form>
        </CardContent>
    </Card>
</template>
