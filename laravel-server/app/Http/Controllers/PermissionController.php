<?php

namespace App\Http\Controllers;

use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\CreatePermissionRequest;
use App\Http\Requests\UpdatePermissionRequest;

class PermissionController extends Controller
{
    public function getListPermission()
    {
        $permissions = Permission::whereNull('deleted_at')->get();
        return response()->json($permissions);
    }

    public function readPermission($id)
    {
        $permission = Permission::whereNull('deleted_at')->findOrFail($id);
        return response()->json($permission);
    }

    public function createPermission(CreatePermissionRequest $request)
    {
    	DB::transaction(function () use ($request) {
            Permission::create(array_merge(
            	$request->all(),
            	['created_by' => (int) request()->attributes->get('userId')]
            ));
    	});

     	return response()->json(['message' => 'Permission created successfully.'], 201);
    }

    public function update(UpdatePermissionRequest $request, $id)
    {
    	DB::transaction(function () use ($request, $id) {
            $permission = Permission::findOrFail($id);
            $permission->update(array_merge(
            	$request->all(),
            	['created_by' => (int) request()->attributes->get('userId')]
            ));
    	});

    	return response()->json(['message' => 'Permission updated successfully.']);
    }

    public function destroy($id)
    {
        DB::transaction(function () use ($id) {
            $permission = Permission::findOrFail($id);
            $permission->forceDelete();
        });

        return response()->json(['message' => 'Permission permanently deleted.']);
    }

    public function softDelete($id)
    {
        DB::transaction(function () use ($id) {
            $permission = Permission::findOrFail($id);
            $permission->updateQuietly(['deleted_by' => request()->attributes->get('userId')]);
            $permission->delete();
        });

        return response()->json(['message' => 'Permission soft deleted successfully.']);
    }

    public function restore($id)
    {
        DB::transaction(function () use ($id) {
            $permission = Permission::findOrFail($id);
            $permission->restore();
            $permission->updateQuietly(['deleted_by' => null]);
        });

        return response()->json(['message' => 'Permission restored successfully.']);
    }
}
