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
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('admin_id');
            $table->string('name', 128);
            $table->string('description', 255)->nullable();
            $table->enum('discount_type', ['percentage', 'fix-amount']);
            $table->integer('amount');
            $table->text('image_url')->nullable();
            $table->integer('code')->default(0);
            $table->datetime('start_datetime')->nullable();
            $table->datetime('end_datetime')->nullable();
            $table->enum('coupon_type', ['private', 'public'])->default('public');
            $table->unsignedInteger('used_count')->default(0);
            $table->softDeletes();
            $table->timestamps();

            // Define foreign key constraint
            $table->foreign('admin_id')
                  ->references('id')
                  ->on('admin')
                  ->onDelete('cascade')
                  ->onUpdate('cascade');
        });

        Schema::table('coupons', function (Blueprint $table) {
            $table->index('name', 'idx_name');
            $table->index('discount_type', 'idx_discount_type');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
