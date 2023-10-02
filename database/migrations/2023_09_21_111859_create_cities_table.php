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
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();
        });

        Schema::create('city_translations', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(\App\Models\City::class);
            $table->string('locale')->index();
            $table->string('name')->nullable();
            $table->unique(['city_id', 'locale'])->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cities');
        Schema::dropIfExists('city_translations');
    }
};
