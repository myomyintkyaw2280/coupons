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
        Schema::create('shops', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('name', 64);
            $table->string('query', 64)->nullable();
            $table->decimal('latitude', 10, 8)->default(0);
            $table->decimal('longitude', 10, 8)->default(0);
            $table->unsignedInteger('zoom')->nullable();
            $table->datetime('deleted_at')->nullable();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('admin_id')
                  ->references('id')
                  ->on('admin')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shops');
    }
};
