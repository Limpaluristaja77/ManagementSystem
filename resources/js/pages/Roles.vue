<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, ShieldCheck, ShieldOff } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Checkbox } from '@/components/ui/checkbox';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Spinner } from '@/components/ui/spinner';

type RolePermission = {
    id: number;
    name: string;
};

type ManagedRole = {
    id: number;
    name: string;
    deactivated_at?: string | null;
    users_count?: number;
    permissions?: RolePermission[];
};

withDefaults(
    defineProps<{
        permissions?: RolePermission[];
        roles?: ManagedRole[];
    }>(),
    {
        permissions: () => [],
        roles: () => [],
    },
);

const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const editingRole = ref<ManagedRole | null>(null);

const createForm = useForm({
    name: '',
    permission_ids: [] as string[],
});

const editForm = useForm({
    name: '',
    permission_ids: [] as string[],
});

const statusForm = useForm({});

const toggleCreatePermission = (permissionId: number, checked: boolean) => {
    const value = String(permissionId);

    if (checked) {
        createForm.permission_ids = [...createForm.permission_ids, value];

        return;
    }

    createForm.permission_ids = createForm.permission_ids.filter(
        (id) => id !== value,
    );
};

const toggleEditPermission = (permissionId: number, checked: boolean) => {
    const value = String(permissionId);

    if (checked) {
        editForm.permission_ids = [...editForm.permission_ids, value];

        return;
    }

    editForm.permission_ids = editForm.permission_ids.filter(
        (id) => id !== value,
    );
};

const submitCreateRole = () => {
    createForm.post('/roles', {
        preserveScroll: true,
        onSuccess: () => {
            isCreateDialogOpen.value = false;
            createForm.reset();
        },
    });
};

const openEditDialog = (role: ManagedRole) => {
    editingRole.value = role;
    editForm.clearErrors();
    editForm.name = role.name;
    editForm.permission_ids = (role.permissions ?? []).map((permission) =>
        String(permission.id),
    );
    isEditDialogOpen.value = true;
};

const submitEditRole = () => {
    if (!editingRole.value) {
        return;
    }

    editForm.put(`/roles/${editingRole.value.id}`, {
        preserveScroll: true,
        onSuccess: () => {
            isEditDialogOpen.value = false;
            editingRole.value = null;
            editForm.reset();
        },
    });
};

const deactivateRole = (role: ManagedRole) => {
    statusForm.post(`/roles/${role.id}/deactivate`, {
        preserveScroll: true,
    });
};

const activateRole = (role: ManagedRole) => {
    statusForm.post(`/roles/${role.id}/activate`, {
        preserveScroll: true,
    });
};

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

            <Button type="button" @click="isCreateDialogOpen = true">
                <Plus />
                New role
            </Button>
        </div>

        <Dialog v-model:open="isCreateDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create role</DialogTitle>
                    <DialogDescription>
                        Add a role and choose the permissions it should have.
                    </DialogDescription>
                </DialogHeader>

                <form class="grid gap-4" @submit.prevent="submitCreateRole">
                    <div class="grid gap-2">
                        <Label for="create-role-name">Role name</Label>
                        <Input
                            id="create-role-name"
                            v-model="createForm.name"
                            type="text"
                            placeholder="manager"
                            required
                        />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Permissions</Label>
                        <div
                            class="grid max-h-72 gap-2 overflow-y-auto rounded-md border p-3 sm:grid-cols-2"
                        >
                            <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="flex items-center gap-2 text-sm"
                            >
                                <Checkbox
                                    :model-value="
                                        createForm.permission_ids.includes(
                                            String(permission.id),
                                        )
                                    "
                                    @update:model-value="
                                        toggleCreatePermission(
                                            permission.id,
                                            Boolean($event),
                                        )
                                    "
                                />
                                <span>{{ permission.name }}</span>
                            </label>
                        </div>
                        <InputError
                            :message="createForm.errors.permission_ids"
                        />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="createForm.processing"
                            @click="isCreateDialogOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="createForm.processing">
                            <Spinner v-if="createForm.processing" />
                            <ShieldCheck v-else />
                            Create role
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit role</DialogTitle>
                    <DialogDescription>
                        Update the role name and assigned permissions.
                    </DialogDescription>
                </DialogHeader>

                <form class="grid gap-4" @submit.prevent="submitEditRole">
                    <div class="grid gap-2">
                        <Label for="edit-role-name">Role name</Label>
                        <Input
                            id="edit-role-name"
                            v-model="editForm.name"
                            type="text"
                            placeholder="manager"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label>Permissions</Label>
                        <div
                            class="grid max-h-72 gap-2 overflow-y-auto rounded-md border p-3 sm:grid-cols-2"
                        >
                            <label
                                v-for="permission in permissions"
                                :key="permission.id"
                                class="flex items-center gap-2 text-sm"
                            >
                                <Checkbox
                                    :model-value="
                                        editForm.permission_ids.includes(
                                            String(permission.id),
                                        )
                                    "
                                    @update:model-value="
                                        toggleEditPermission(
                                            permission.id,
                                            Boolean($event),
                                        )
                                    "
                                />
                                <span>{{ permission.name }}</span>
                            </label>
                        </div>
                        <InputError :message="editForm.errors.permission_ids" />
                    </div>

                    <DialogFooter>
                        <Button
                            type="button"
                            variant="outline"
                            :disabled="editForm.processing"
                            @click="isEditDialogOpen = false"
                        >
                            Cancel
                        </Button>
                        <Button type="submit" :disabled="editForm.processing">
                            <Spinner v-if="editForm.processing" />
                            <Pencil v-else />
                            Save changes
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

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
                                <th class="px-3 py-2 font-medium">
                                    Permissions
                                </th>
                                <th class="px-3 py-2 font-medium">Status</th>
                                <th class="px-3 py-2 font-medium"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr
                                v-for="role in roles"
                                :key="role.id"
                                class="border-t align-top"
                            >
                                <td class="px-3 py-2 font-medium">
                                    {{ role.name }}
                                </td>
                                <td class="px-3 py-2">
                                    {{ role.users_count ?? 0 }}
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex max-w-xl flex-wrap gap-1">
                                        <Badge
                                            v-for="permission in role.permissions ??
                                            []"
                                            :key="permission.id"
                                            variant="outline"
                                        >
                                            {{ permission.name }}
                                        </Badge>
                                    </div>
                                </td>
                                <td class="px-3 py-2">
                                    <Badge
                                        :variant="
                                            role.deactivated_at
                                                ? 'secondary'
                                                : 'default'
                                        "
                                    >
                                        {{
                                            role.deactivated_at
                                                ? 'Inactive'
                                                : 'Active'
                                        }}
                                    </Badge>
                                </td>
                                <td class="px-3 py-2">
                                    <div class="flex justify-end gap-2">
                                        <Button
                                            size="sm"
                                            variant="outline"
                                            type="button"
                                            @click="openEditDialog(role)"
                                        >
                                            <Pencil />
                                            Edit
                                        </Button>
                                        <Button
                                            v-if="role.deactivated_at"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            :disabled="statusForm.processing"
                                            @click="activateRole(role)"
                                        >
                                            <RotateCcw />
                                            Activate
                                        </Button>
                                        <Button
                                            v-else
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            :disabled="statusForm.processing"
                                            @click="deactivateRole(role)"
                                        >
                                            <ShieldOff />
                                            Deactivate
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="roles.length === 0">
                                <td
                                    class="px-3 py-8 text-center text-muted-foreground"
                                    colspan="5"
                                >
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
