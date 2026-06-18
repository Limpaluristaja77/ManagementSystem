<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Roles', [
            'roles' => Role::query()
                ->with('permissions:id,name')
                ->withCount('users')
                ->orderBy('name')
                ->get(['id', 'name']),
        ]);
    }
}
