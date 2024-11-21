<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDeletedAtToRolesAndPermissions extends Migration
{
    public function up()
    {
        Schema::table('roles_and_permissions', function (Blueprint $table) {
            $table->softDeletes();
        });
    }

    public function down()
    {
        Schema::table('roles_and_permissions', function (Blueprint $table) {
            $table->dropSoftDeletes();
        });
    }
}
