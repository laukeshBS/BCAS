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
        Schema::create('circulars', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->string('slugs')->nullable();
            $table->string('page_url')->nullable();
            $table->integer('is_new')->nullable();
            $table->integer('language');
            $table->integer('circulars_type');
            $table->string('metakeyword')->nullable();
            $table->string('metadescription')->nullable();
            $table->longtext('description')->nullable();
            $table->string('doc_upload')->nullable();
            $table->string('img_upload')->nullable();
            $table->string('circulars_link')->nullable();
            $table->integer('circulars_status');
            $table->integer('created_by');
            $table->date('startdate');
            $table->date('enddate');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('circulars', function (Blueprint $table) {
            //
        });
    }
};
