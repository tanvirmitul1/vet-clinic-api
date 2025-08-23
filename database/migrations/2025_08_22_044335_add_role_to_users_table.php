<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRoleToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Only add role_id if it doesn't exist
            if (!Schema::hasColumn('users', 'role_id')) {
                $table->unsignedBigInteger('role_id')->default(4)->after('id'); // Pet Owner default

                // Add foreign key if roles table exists
                if (Schema::hasTable('roles')) {
                    $table->foreign('role_id')->references('id')->on('roles');
                }
            }

            // Add other columns safely
            if (!Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('role_id');
            }

            if (!Schema::hasColumn('users', 'address')) {
                $table->text('address')->nullable()->after('phone');
            }

            if (!Schema::hasColumn('users', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('address');
            }
        });
    }

public function down()
{
    Schema::table('users', function (Blueprint $table) {
        // Check if foreign key exists before dropping
        $foreignKeys = \DB::select("
            SELECT CONSTRAINT_NAME 
            FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
            WHERE TABLE_SCHEMA = DATABASE()
              AND TABLE_NAME = 'users'
              AND COLUMN_NAME = 'role_id'
              AND REFERENCED_TABLE_NAME = 'roles'
        ");

        if (!empty($foreignKeys)) {
            $table->dropForeign(['role_id']);
        }

        // Drop columns if they exist
        if (Schema::hasColumn('users', 'role_id')) {
            $table->dropColumn('role_id');
        }
        if (Schema::hasColumn('users', 'phone')) {
            $table->dropColumn('phone');
        }
        if (Schema::hasColumn('users', 'address')) {
            $table->dropColumn('address');
        }
        if (Schema::hasColumn('users', 'is_active')) {
            $table->dropColumn('is_active');
        }
    });
}



}
