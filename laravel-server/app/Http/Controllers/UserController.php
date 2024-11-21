<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\UsersAndRoles;

use Illuminate\Http\Request;

class UserController extends Controller
{
/*    public function getListUser()
    {
        $users = User::all();
        return response()->json($users);
    }

    public function getUserRoles($id)
    {
        $user = User::findOrFail($id);
        $roles = $user->roles;
        return response()->json($roles);
    }*/

    public function getListUser()
    {
    	$users = User::whereNull('deleted_at')->get();
    	return response()->json($users);
    }

    public function getUserRoles($id)
    {
        $user = User::findOrFail($id);

        $roles = $user->roles()->wherePivotNull('deleted_at')->get();
    	return response()->json($roles);
    }


    public function assignRole(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $createdBy = request()->attributes->get('userId');

        $user->roles()->syncWithoutDetaching($request->role_ids);

        foreach ($request->role_ids as $roleId) {
            $user->roles()->updateExistingPivot($roleId, ['created_by' => $createdBy]);
        }

        return response()->json(['message' => 'Roles assigned successfully.']);
    }

    public function deleteRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);

        $deletedBy = request()->attributes->get('userId');

        $user->roles()->updateExistingPivot($roleId, ['deleted_by' => $deletedBy]);

        $user->roles()->detach($roleId);

        return response()->json(['message' => 'Role removed successfully.']);
    }

    public function softDeleteRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);

        $deletedBy = request()->attributes->get('userId');

        $user->roles()->updateExistingPivot($roleId, [
            'deleted_at' => now(),
            'deleted_by' => $deletedBy
        ]);

        return response()->json(['message' => 'Role soft deleted successfully.']);
    }

    public function restoreRole($userId, $roleId)
    {
        $user = User::findOrFail($userId);

        $user->roles()->updateExistingPivot($roleId, [
            'deleted_at' => null,
            'deleted_by' => null
        ]);

        return response()->json(['message' => 'Role restored successfully.']);
    }
}
