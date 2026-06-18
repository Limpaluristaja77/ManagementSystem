<script setup lang="ts">
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Head } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, ShieldOff } from '@lucide/vue';

type RolePermission = {
    id: number;
    name: string;
};

type ManagedRole = {
    id: number;
    name: string;
    is_active?: boolean;
    users_count?: number;
    permissions?: RolePermission[];
};

withDefaults(
    defineProps<{
        roles?: ManagedRole[];
    }>(),
    {
        roles: () => [],
    },
);

defineOptions({
    layout: {
        breadcrumbs: [
            {
                title: 'Roles',
                href: '/roles',
            },
        ],
    },
});
</script>

<template>
    <Head title="Roles" />

    <div class="flex h-full flex-1 flex-col gap-4 overflow-x-auto p-4">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold tracking-normal">Roles</h1>
                <p class="text-sm text-muted-foreground">
                    Configure module access for managers and users.
                </p>
            </div>

            <Button type="button">
                <Plus />
                New role
            </Button>
        </div>

        <Card>
            <CardHeader>
                <CardTitle>All roles</CardTitle>
            </CardHeader>
            <CardContent>
                <div class="overflow-hidden rounded-md border">
                    <table class="w-full text-sm">
                        <thead class="bg-muted/60 text-left">
                            <tr>
                                <th class="px-3 py-2 font-medium">Role</th>
                                <th class="px-3 py-2 font-medium">Users</th>
                                <th class="px-3 py-2 font-medium">Permissions</th>
                                <th class="px-3 py-2 font-medium">Status</th>
                                <th class="px-3 py-2 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="role in roles" :key="role.id" class="border-t align-top">
                                <td class="px-3 py-2 font-medium">{{ role.name }}</td>
                                <td class="px-3 py-2">{{ role.users_count ?? 0 }}</td>
                                <td class="px-3 py-2">
                                    <div class="flex max-w-xl flex-wrap gap-1">
                                        <Badge
                                            v-for="permission in role.permissions ?? []"
                                            :key="permission.id"
                                            variant="outline"
                                        >
                                            {{ permission.name }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <Badge :variant="role.is_active === false ? 'secondary' : 'default'">
                                        {{ role.is_active === false ? 'Inactive' : 'Active' }}
                                    </Badge>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
                                        <Button size="sm" variant="outline" type="button">
                                            <Pencil />
                                            Edit
                                        </Button>
                                        <Button
                                            v-if="role.is_active === false"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                        >
                                            <RotateCcw />
                                            Activate
                                        </Button>
                                        <Button v-else size="sm" variant="secondary" type="button">
                                            <ShieldOff />
                                            Deactivate
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="roles.length === 0">
                                <td class="px-3 py-8 text-center text-muted-foreground" colspan="5">
                                    No roles found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </CardContent>
        </Card>
    </div>
</template>
