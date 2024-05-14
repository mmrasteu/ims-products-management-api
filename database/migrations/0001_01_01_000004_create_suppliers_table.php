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
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('cif')->unique();
            $table->string('description', 255)->nullable();
            $table->string('email')->unique();
            $table->string('phone', 9)->nullable();
            $table->string('address')->nullable();
            $table->string('location')->nullable();
            $table->string('zip_code')->nullable();
            $table->string('contact_name')->nullable();
            $table->string('contact_title')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
