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
        Schema::create('menus', function (Blueprint $table) {
            $table->id();
            $table->integer('menu_type');
            $table->integer('menu_child_id')->nullable();
            $table->integer('menu_position');
            $table->integer('language_id');
            $table->string('menu_name')->nullable();
            $table->string('menu_url')->nullable();
            $table->string('menu_title')->nullable();
            $table->string('menu_keyword')->nullable();
            $table->string('menu_description')->nullable();
            $table->longtext('content')->nullable();
            $table->string('doc_upload')->nullable();
            $table->string('img_upload')->nullable();
            $table->string('menu_links')->nullable();
            $table->time('start_date')->nullable();
            $table->time('end_date')->nullable();
            $table->tinyInteger('approve_status')->default(0);
            $table->integer('created_by');
            $table->integer('page_order')->nullable();
            $table->integer('current_version')->nullable();
            $table->longtext('welcomedescription')->nullable();
            $table->string('banner_img')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('menus');
    }
};
