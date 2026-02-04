<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('titles', function (Blueprint $table) {
        $table->id();
        $table->string('type'); // movie | series
        $table->string('name');
        $table->string('slug')->unique();
        $table->text('overview')->nullable();
        $table->string('poster_url')->nullable();
        $table->date('release_date')->nullable();
        $table->float('popularity')->default(0);
        $table->integer('featured_rank')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('titles');
    }
};
