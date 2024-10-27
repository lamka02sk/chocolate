<?php

use App\Models\User;
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
        Schema::create('invoices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(User::class)->constrained();
            $table->string('title', 256)->index();
            $table->string('partner', 256)->index();
            $table->enum('type', ['received', 'issued'])->index();
            $table->text('notes')->nullable();
            $table->json('tags')->nullable()->index();
            $table->json('attachments')->nullable();
            $table->json('attachments_file_names')->nullable();
            $table->date('date_issue')->nullable();
            $table->date('date_paid')->nullable();
            $table->softDeletes()->index();
            $table->timestampsTz();

            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
