<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('status_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained('complaints')->cascadeOnDelete();
            $table->foreignId('changed_by')->constrained('users');
            $table->string('old_status', 50)->nullable();
            $table->string('new_status', 50);
            $table->text('remarks')->nullable();
            $table->timestamps();
            // No softDeletes — audit trail must be permanent
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('status_logs');
    }
};
