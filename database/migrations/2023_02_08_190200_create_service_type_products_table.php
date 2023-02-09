<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('service_type_products', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('service_type_id');
            $table->string('label');
            $table->unsignedInteger('quantity')->nullable();

            $table->foreign('service_type_id')->references('id')->on('service_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('service_type_products');
    }
};
