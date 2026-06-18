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
            if (! Schema::hasColumn('users', 'xp_total')) {
                $table->unsignedInteger('xp_total')->default(0)->after('email');
            }
            if (! Schema::hasColumn('users', 'level')) {
                $table->unsignedInteger('level')->default(1)->after('xp_total');
            }
            if (! Schema::hasColumn('users', 'user_level')) {
                $table->string('user_level')->default('beginner')->after('level'); // beginner, intermediate, advanced
            }
            if (! Schema::hasColumn('users', 'phone')) {
                $table->string('phone')->nullable()->after('user_level');
            }
            if (! Schema::hasColumn('users', 'title')) {
                $table->string('title')->nullable()->after('phone');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['xp_total', 'level', 'user_level', 'phone', 'title']);
        });
    }
};
