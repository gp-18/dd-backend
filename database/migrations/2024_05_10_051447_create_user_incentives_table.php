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
        Schema::create('user_incentives', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('excel_user_ops')->onDelete('cascade');   
            $table->string('bo_name');
            $table->string('bo_email')->unique();
            $table->string('headquarter');
            $table->bigInteger('april_may_june_target')->default(0);
            $table->bigInteger('july_aug_sept_target')->default(0);;
            $table->bigInteger('oct_nov_dec_target')->default(0);;
            $table->bigInteger('april_may_june_incentive')->default(0);;
            $table->bigInteger('july_aug_sept_incentive')->default(0);;
            $table->bigInteger('oct_nov_dec_incentive')->default(0);;
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_incentives');
    }
};
