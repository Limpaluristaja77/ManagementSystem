<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Pencil, Plus, RotateCcw, UserPlus, UserX } from '@lucide/vue';
import { ref } from 'vue';
import InputError from '@/components/InputError.vue';
import PasswordInput from '@/components/PasswordInput.vue';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import {
    Dialog,
    DialogContent,
    DialogDescription,
    DialogFooter,
    DialogHeader,
    DialogTitle,
} from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import {
    Select,
    SelectContent,
    SelectItem,
    SelectTrigger,
    SelectValue,
} from '@/components/ui/select';
import { Spinner } from '@/components/ui/spinner';
import type { ManagedUser, UserRole } from '@/types/user';

withDefaults(
    defineProps<{
        roles?: UserRole[];
        users?: ManagedUser[];
    }>(),
    {
        roles: () => [],
        users: () => [],
    },
);

const isCreateDialogOpen = ref(false);
const isEditDialogOpen = ref(false);
const editingUser = ref<ManagedUser | null>(null);

const createForm = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role_id: 'none',
});

const submitCreateUser = () => {
    createForm
        .transform((data) => ({
            ...data,
            role_id: data.role_id === 'none' ? null : data.role_id,
        }))
        .post('/users', {
            preserveScroll: true,
            onSuccess: () => {
                isCreateDialogOpen.value = false;
                createForm.reset();
                createForm.role_id = 'none';
            },
        });
};

const editForm = useForm({
    name: '',
    role_id: 'none',
});

const statusForm = useForm({});

const openEditDialog = (user: ManagedUser) => {
    editingUser.value = user;
    editForm.clearErrors();
    editForm.name = user.name;
    editForm.role_id = user.roles?.[0]?.id ? String(user.roles[0].id) : 'none';
    isEditDialogOpen.value = true;
};

const submitEditUser = () => {
    if (!editingUser.value) {
        return;
    }

    editForm
        .transform((data) => ({
            ...data,
            role_id: data.role_id === 'none' ? null : data.role_id,
        }))
        .put(`/users/${editingUser.value.id}`, {
            preserveScroll: true,
            onSuccess: () => {
                isEditDialogOpen.value = false;
                editingUser.value = null;
                editForm.reset();
                editForm.role_id = 'none';
            },
        });
};

const deactivateUser = (user: ManagedUser) => {
    statusForm.post(`/users/${user.id}/deactivate`, {
        preserveScroll: true,
    });
};

const activateUser = (user: ManagedUser) => {
    statusForm.post(`/users/${user.id}/activate`, {
        preserveScroll: true,
    });
};

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

            <Button type="button" @click="isCreateDialogOpen = true">
                <Plus />
                New user
            </Button>
        </div>

        <Dialog v-model:open="isCreateDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Create user</DialogTitle>
                    <DialogDescription>
                        Add a new account and optionally assign an initial role.
                    </DialogDescription>
                </DialogHeader>

                <form class="grid gap-4" @submit.prevent="submitCreateUser">
                    <div class="grid gap-2">
                        <Label for="create-user-name">Name</Label>
                        <Input
                            id="create-user-name"
                            v-model="createForm.name"
                            type="text"
                            autocomplete="name"
                            placeholder="Full name"
                            required
                        />
                        <InputError :message="createForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-user-email">Email address</Label>
                        <Input
                            id="create-user-email"
                            v-model="createForm.email"
                            type="email"
                            autocomplete="email"
                            placeholder="email@example.com"
                            required
                        />
                        <InputError :message="createForm.errors.email" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-user-password">Password</Label>
                        <PasswordInput
                            id="create-user-password"
                            v-model="createForm.password"
                            autocomplete="new-password"
                            placeholder="Password"
                            required
                        />
                        <InputError :message="createForm.errors.password" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-user-password-confirmation"
                            >Confirm password</Label
                        >
                        <PasswordInput
                            id="create-user-password-confirmation"
                            v-model="createForm.password_confirmation"
                            autocomplete="new-password"
                            placeholder="Confirm password"
                            required
                        />
                        <InputError
                            :message="createForm.errors.password_confirmation"
                        />
                    </div>

                    <div class="grid gap-2">
                        <Label for="create-user-role">Role</Label>
                        <Select v-model="createForm.role_id">
                            <SelectTrigger id="create-user-role" class="w-full">
                                <SelectValue placeholder="No role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">No role</SelectItem>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="String(role.id)"
                                >
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="createForm.errors.role_id" />
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
                            <UserPlus v-else />
                            Create user
                        </Button>
                    </DialogFooter>
                </form>
            </DialogContent>
        </Dialog>

        <Dialog v-model:open="isEditDialogOpen">
            <DialogContent>
                <DialogHeader>
                    <DialogTitle>Edit user</DialogTitle>
                    <DialogDescription>
                        Update the user name and assigned role.
                    </DialogDescription>
                </DialogHeader>

                <form class="grid gap-4" @submit.prevent="submitEditUser">
                    <div class="grid gap-2">
                        <Label for="edit-user-name">Name</Label>
                        <Input
                            id="edit-user-name"
                            v-model="editForm.name"
                            type="text"
                            autocomplete="name"
                            placeholder="Full name"
                            required
                        />
                        <InputError :message="editForm.errors.name" />
                    </div>

                    <div class="grid gap-2">
                        <Label for="edit-user-role">Role</Label>
                        <Select v-model="editForm.role_id">
                            <SelectTrigger id="edit-user-role" class="w-full">
                                <SelectValue placeholder="No role" />
                            </SelectTrigger>
                            <SelectContent>
                                <SelectItem value="none">No role</SelectItem>
                                <SelectItem
                                    v-for="role in roles"
                                    :key="role.id"
                                    :value="String(role.id)"
                                >
                                    {{ role.name }}
                                </SelectItem>
                            </SelectContent>
                        </Select>
                        <InputError :message="editForm.errors.role_id" />
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
                            <tr
                                v-for="user in users"
                                :key="user.id"
                                class="border-t"
                            >
                                <td class="px-3 py-2 font-medium">
                                    {{ user.name }}
                                </td>
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
                                    <Badge
                                        :variant="
                                            user.deactivated_at
                                                ? 'secondary'
                                                : 'default'
                                        "
                                    >
                                        {{
                                            user.deactivated_at
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
                                            @click="openEditDialog(user)"
                                        >
                                            <Pencil />
                                            Edit
                                        </Button>
                                        <Button
                                            v-if="user.deactivated_at"
                                            size="sm"
                                            variant="secondary"
                                            type="button"
                                            :disabled="statusForm.processing"
                                            @click="activateUser(user)"
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
                                            @click="deactivateUser(user)"
                                        >
                                            <UserX />
                                            Deactivate
                                        </Button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.length === 0">
                                <td
                                    class="px-3 py-8 text-center text-muted-foreground"
                                    colspan="5"
                                >
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
