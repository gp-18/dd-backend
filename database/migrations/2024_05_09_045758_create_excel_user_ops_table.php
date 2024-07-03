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
        Schema::create('excel_user_ops', function (Blueprint $table) {
            $table->id();
            $table->string('bo_name');
            $table->string('abm_name');
            $table->string('rsm_name');
            $table->string('nsm_name');
            $table->string('gpm_name');
            $table->string('bo_email')->unique();
            $table->string('abm_email');
            $table->string('rsm_email');
            $table->string('nsm_email');
            $table->string('gpm_email');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excel_user_ops');
    }
};
