<script setup lang="ts">
import InputError from '@/components/InputError.vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import type { AttendanceRecord } from '@/types/attendance';
import { Check, Clock } from '@lucide/vue';
import type { InertiaForm } from '@inertiajs/vue3';

defineProps<{
    selectedRecord: AttendanceRecord | null;
    form: InertiaForm<{
        check_in_time: string;
        check_out_time: string;
        reason_for_change: string;
    }>;
}>();

const emit = defineEmits<{
    submit: [];
}>();
</script>

<template>
    <Card>
        <CardHeader>
            <CardTitle class="flex items-center gap-2">
                <Clock class="size-4" />
                Change record
            </CardTitle>
            <CardDescription>
                A reason is required for every saved change.
            </CardDescription>
        </CardHeader>
        <CardContent>
            <form class="space-y-4" @submit.prevent="emit('submit')">
                <div class="grid gap-2">
                    <Label for="check_in_time">Check in</Label>
                    <Input
                        id="check_in_time"
                        v-model="form.check_in_time"
                        type="time"
                        :disabled="!selectedRecord"
                    />
                    <InputError :message="form.errors.check_in_time" />
                </div>

                <div class="grid gap-2">
                    <Label for="check_out_time">Check out</Label>
                    <Input
                        id="check_out_time"
                        v-model="form.check_out_time"
                        type="time"
                        :disabled="!selectedRecord"
                    />
                    <InputError :message="form.errors.check_out_time" />
                </div>

                <div class="grid gap-2">
                    <Label for="reason_for_change">Reason for change</Label>
                    <textarea
                        id="reason_for_change"
                        v-model="form.reason_for_change"
                        class="min-h-24 w-full rounded-md border border-input bg-transparent px-3 py-2 text-sm shadow-xs outline-none transition-[color,box-shadow] focus-visible:border-ring focus-visible:ring-[3px] focus-visible:ring-ring/50 disabled:cursor-not-allowed disabled:opacity-50"
                        :disabled="!selectedRecord"
                    />
                    <InputError :message="form.errors.reason_for_change" />
                </div>

                <Button
                    type="submit"
                    class="w-full"
                    :disabled="!selectedRecord || form.processing"
                >
                    <Check />
                    Save change
                </Button>
            </form>
        </CardContent>
    </Card>
</template>
