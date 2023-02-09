<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('service_types', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('type_category_id');
            $table->string('label');
            $table->unsignedDouble('price');
            $table->boolean('quantifiable');
            $table->boolean('available');
            $table->timestamps();

            $table->foreign('type_category_id')->references('id')->on('service_type_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('service_types');
    }
};
