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
        Schema::create('bookings', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('property_id');
            $table->uuid('cleaner_id')->nullable();
            $table->timestamp('start_time');
            $table->timestamp('end_time');
            $table->string('secondary_contact')->nullable();
            $table->longText('additional_information')->nullable();
            $table->longText('cleaner_remarks')->nullable();
            $table->timestamp('rejected_at')->nullable();
            $table->timestamp('complete_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
