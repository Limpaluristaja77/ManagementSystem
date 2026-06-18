<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, UserX } from '@lucide/vue';

type UserRole = {
    id: number;
    name: string;
};

type ManagedUser = {
    id: number;
    name: string;
    email: string;
    is_active?: boolean;
    deactivated_at?: string | null;
    roles?: UserRole[];
};

withDefaults(
    defineProps<{
        users?: ManagedUser[];
    }>(),
    {
        users: () => [],
    },
);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Users',
                href: '/users',
            },
        ],
    },
});
</script>

<template>
    <Head title="Users" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Users</h1>
                <p class="text-sm text-muted-foreground">
                    Manage accounts, assigned roles, and access status.
                </p>
            </div>

            <Button type="button">
                <Plus />
                New user
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>All users</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-hidden rounded-md border">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-3 py-2 font-medium">Name</th>
                                <th class="px-3 py-2 font-medium">Email</th>
                                <th class="px-3 py-2 font-medium">Roles</th>
                                <th class="px-3 py-2 font-medium">Status</th>
                                <th class="px-3 py-2 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="user in users" :key="user.id" class="border-t">
                                <td class="px-3 py-2 font-medium">{{ user.name }}</td>
                                <td class="px-3 py-2">{{ user.email }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex flex-wrap gap-1">
                                        <Badge
                                            v-for="role in user.roles ?? []"
                                            :key="role.id"
                                            variant="outline"
                                        >
                                            {{ role.name }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <Badge :variant="user.is_active === false ? 'secondary' : 'default'">
                                        {{ user.is_active === false ? 'Inactive' : 'Active' }}
                                    </Badge>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
                                        <Button size="sm" variant="outline" type="button">
                                            <Pencil />
                                            Edit
                                        </Button>
                                        <Button
                                            v-if="user.is_active === false"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                        >
                                            <RotateCcw />
                                            Activate
                                        </Button>
                                        <Button v-else size="sm" variant="secondary" type="button">
                                            <UserX />
                                            Deactivate
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td class="px-3 py-8 text-center text-muted-foreground" colspan="5">
                                    No users found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
