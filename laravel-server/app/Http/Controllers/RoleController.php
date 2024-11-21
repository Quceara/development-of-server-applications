<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\RolesAndPermissions;
use App\Http\Requests\CreateRoleRequest;
use App\Http\Requests\UpdateRoleRequest;
use App\Http\Requests\SoftDeletePermissionsRequest;

class RoleController extends Controller
{
    public function getListRole()
    {
        $roles = Role::whereNull('roles.deleted_at')->get();
        return response()->json($roles);
    }

    public function readRole($id)
    {
        $role = Role::whereNull('roles.deleted_at')->with(['permissions' => function ($query) {
            $query->whereNull('permissions.deleted_at')
                ->whereNull('roles_and_permissions.deleted_at');
        }])->findOrFail($id);

        return response()->json($role);
    }

    public function createRole(CreateRoleRequest $request)
    {
    	DB::transaction(function () use ($request) {
            $role = Role::create(array_merge(
            	$request->only(['name', 'slug', 'description']),
            	['created_by' => (int) $request->attributes->get('userId')]
            ));

            if ($request->has('permission_ids')) {
            	$role->permissions()->syncWithPivotValues($request->permission_ids, [
                    'created_by' => $request->attributes->get('userId')
            	]);
            }
    	});

    	return response()->json(['message' => 'Role created successfully.'], 201);
    }

    public function update(UpdateRoleRequest $request, $id)
    {
    	DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);
            $roleData = $request->only(['name', 'slug', 'description']);

            if (!empty($roleData)) {
                $roleData['created_by'] = (int) $request->attributes->get('userId');
                $role->update($roleData);
            }

            if ($request->has('permission_ids')) {
                $role->permissions()->syncWithoutDetaching(
                    $request->permission_ids,
                    ['created_by' => (int) $request->attributes->get('userId')]
            	);
            }
    	});

    	return response()->json(['message' => 'Role updated successfully.']);
    }

    public function softDeletePermissions(SoftDeletePermissionsRequest $request, $id)
    {
    	DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);

            if ($request->has('permission_ids')) {
            	foreach ($request->soft_delete_permission_ids as $permissionId) {
                    $role->permissions()->updateExistingPivot($permissionId, [
                    	'deleted_at' => now(),
                    	'deleted_by' => $request->attributes->get('userId')
                    ]);
            	}
            }
    	});

    	return response()->json(['message' => 'Permissions soft-deleted successfully.']);
    }


    public function removePermissions(SoftDeletePermissionsRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);

            if ($request->has('permission_ids')) {
                $role->permissions()->detach($request->remove_permission_ids);
            }
        });

        return response()->json(['message' => 'Permissions removed successfully.']);
    }

    public function destroy($id)
    {
    	DB::transaction(function () use ($id) {
            $role = Role::findOrFail($id);
            $role->forceDelete();
    	});

    	return response()->json(['message' => 'Role deleted permanently.']);
    }

    public function softDelete($id)
    {
    	DB::transaction(function () use ($id) {
            $role = Role::findOrFail($id);

            $role->updateQuietly(['deleted_by' => request()->attributes->get('userId')]);

            $role->delete();
    	});

    	return response()->json(['message' => 'Role soft deleted successfully.']);
    }

    public function restore($id)
    {
    	DB::transaction(function () use ($id) {
            $role = Role::withTrashed()->findOrFail($id);
            $role->restore();
	    $role->updateQuietly(['deleted_by' => null]);
    	});

    	return response()->json(['message' => 'Role restored successfully.']);
    }


    public function permissionRestore(SoftDeletePermissionsRequest $request, $id)
    {
        DB::transaction(function () use ($request, $id) {
            $role = Role::findOrFail($id);

            if ($request->has('permission_ids')) {
                foreach ($request->restore_permission_ids as $permissionId) {
                    $role->permissions()->updateExistingPivot($permissionId, [
                        'deleted_at' => null,
                        'deleted_by' => null
                    ]);
                }
            }
        });

        return response()->json(['message' => 'Permissions restored successfully.']);
    }
}
