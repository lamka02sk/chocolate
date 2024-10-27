<?php

use App\Models\Invoice;
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
        Schema::create('invoice_items', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignIdFor(Invoice::class)->constrained();
            $table->string('name', 256);
            $table->decimal('quantity', 14, 5)->default(1);
            $table->string('unit', 8)->nullable();
            $table->decimal('price', 13, 5)->index();
            $table->integer('sort', unsigned: true);
            $table->softDeletes()->index();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
