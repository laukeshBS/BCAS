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
        Schema::create('audit_trails', function (Blueprint $table) {
            $table->id();
            $table->string('action_name')->nullable();
            $table->string('action_type')->nullable();
            $table->string('module_item_title')->nullable();
            $table->integer('module_item_id')->nullable();
            $table->integer('action_by')->nullable();
            $table->integer('lang_id');
            $table->integer('approve_status');
            $table->string('old_data')->nullable();
            $table->string('new_data')->nullable();
            $table->string('action_date');
            $table->string('ip_address');
            $table->string('action_by_role');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_trails');
    }
};
