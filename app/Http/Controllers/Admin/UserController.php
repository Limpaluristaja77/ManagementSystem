<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Users', [
            'roles' => Role::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'users' => User::query()
                ->with('roles:id,name')
                ->orderBy('name')
                ->get(['id', 'name', 'email', 'deactivated_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', Password::default(), 'confirmed'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
        ]);

        if (! empty($validated['role_id'])) {
            $user->assignRole(Role::query()->findOrFail($validated['role_id']));
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User created.')]);

        return to_route('users.index');
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'role_id' => ['nullable', 'integer', 'exists:roles,id'],
        ]);

        $user->update([
            'name' => $validated['name'],
        ]);

        if (! empty($validated['role_id'])) {
            $user->syncRoles([Role::query()->findOrFail($validated['role_id'])]);
        } else {
            $user->syncRoles([]);
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User updated.')]);

        return to_route('users.index');
    }

    public function deactivate(User $user): RedirectResponse
    {
        $user->update([
            'deactivated_at' => now(),
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User deactivated.')]);

        return to_route('users.index');
    }

    public function activate(User $user): RedirectResponse
    {
        $user->update([
            'deactivated_at' => null,
        ]);

        Inertia::flash('toast', ['type' => 'success', 'message' => __('User activated.')]);

        return to_route('users.index');
    }
}
