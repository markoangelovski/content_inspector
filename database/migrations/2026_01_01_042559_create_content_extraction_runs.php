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
        Schema::create('content_extraction_runs', function (Blueprint $table) {
            $table->ulid('id')->primary();

            $table->ulid('website_id')->index();
            $table->string('status', 32);

            $table->integer('total_pages')->default(0);
            $table->integer('processed_pages')->default(0);
            $table->integer('failed_pages')->default(0);

            $table->string('extractor_version', 64);
            $table->jsonb('config')->default('{}');

            $table->timestampTz('started_at')->nullable();
            $table->timestampTz('finished_at')->nullable();

            $table->ulid('created_by')->nullable();

            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
