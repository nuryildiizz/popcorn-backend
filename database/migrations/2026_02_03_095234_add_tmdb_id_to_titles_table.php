<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->unsignedBigInteger('tmdb_id')->nullable()->unique()->after('id');
            // istersen: index yeterli olsun dersen unique yerine ->index()
        });
    }

    public function down(): void
    {
        Schema::table('titles', function (Blueprint $table) {
            $table->dropUnique(['tmdb_id']);
            $table->dropColumn('tmdb_id');
        });
    }
};
