<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('method', 10);
            $table->string('path', 500);
            $table->jsonb('query_params')->nullable();
            $table->jsonb('request_body')->nullable();
            $table->string('ip_address', 45);
            $table->text('user_agent')->nullable();
            $table->jsonb('request_headers')->nullable();
            $table->smallInteger('response_status');
            $table->integer('response_time_ms');
            $table->timestamp('created_at');

            $table->index('user_id');
            $table->index('path');
            $table->index('response_status');
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_logs');
    }
};