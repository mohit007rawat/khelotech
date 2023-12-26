<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('middle_name')->nullable()->after('name');
            $table->enum('role_id', [1, 2])->default(2)->after('id');
            $table->string('last_name')->nullable()->after('middle_name');
            $table->string('mobile_number')->nullable()->after('last_name');
            $table->string('state')->nullable()->after('password');
            $table->string('city')->nullable()->after('state');
            $table->string('address')->nullable()->after('city');
            $table->string('profile_photo')->nullable()->after('address');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            //
        });
    }
};
