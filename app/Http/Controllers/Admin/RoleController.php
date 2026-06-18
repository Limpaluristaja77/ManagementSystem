<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Roles', [
            'permissions' => Permission::query()
                ->orderBy('name')
                ->get(['id', 'name']),
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->withCount('users')
                ->orderBy('name')
                ->get(['id', 'name', 'deactivated_at']),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', Rule::unique('roles', 'name')->where('guard_name', 'web')],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'guard_name' => 'web',
        ]);

        $role->syncPermissions($this->permissionNamesFromIds($validated['permission_ids'] ?? [], $role->guard_name));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role created.')]);

        return to_route('roles.index');
    }

    public function update(Request $request, Role $role): RedirectResponse
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles', 'name')
                    ->where('guard_name', $role->guard_name)
                    ->ignore($role->id),
            ],
            'permission_ids' => ['array'],
            'permission_ids.*' => ['integer', 'exists:permissions,id'],
        ]);

        $role->update([
            'name' => $validated['name'],
        ]);

        $role->syncPermissions($this->permissionNamesFromIds($validated['permission_ids'] ?? [], $role->guard_name));

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role updated.')]);

        return to_route('roles.index');
    }

    public function deactivate(Role $role): RedirectResponse
    {
        $role->forceFill([
            'deactivated_at' => now(),
        ])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role deactivated.')]);

        return to_route('roles.index');
    }

    public function activate(Role $role): RedirectResponse
    {
        $role->forceFill([
            'deactivated_at' => null,
        ])->save();

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Role activated.')]);

        return to_route('roles.index');
    }

    /**
     * @param  array<int, int|string>  $permissionIds
     */
    private function permissionNamesFromIds(array $permissionIds, string $guardName): array
    {
        return Permission::query()
            ->whereIn('id', $permissionIds)
            ->where('guard_name', $guardName)
            ->pluck('name')
            ->all();
    }
}
