<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('api_logs', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('method', 10);
            $table->text('url');
            $table->string('path')->index();
            $table->json('headers');
            $table->json('query_params');
            $table->longText('request_body')->nullable();
            $table->integer('response_status')->index();
            $table->longText('response_body')->nullable();
            $table->integer('duration_ms');
            $table->string('ip')->nullable();
            $table->string('user_id')->nullable()->index();
            $table->timestamp('created_at')->index();
        });

        Schema::create('api_log_replays', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('api_log_id')->index();
            $table->integer('response_status');
            $table->longText('response_body')->nullable();
            $table->integer('duration_ms');
            $table->timestamp('created_at');

            $table->foreign('api_log_id')->references('id')->on('api_logs')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('api_log_replays');
        Schema::dropIfExists('api_logs');
    }
};
