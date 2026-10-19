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
        Schema::create('job_applications', function (Blueprint $table) {
            $table->id();
            // Each application belongs to one user; deleting the user deletes their applications.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('company');
            $table->string('position');
            $table->string('status', 20)->default('applied');
            $table->string('location')->nullable();
            $table->string('job_url', 2048)->nullable();
            $table->date('applied_on')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Speeds up "my applications filtered by status".
            $table->index(['user_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('job_applications');
    }
};
