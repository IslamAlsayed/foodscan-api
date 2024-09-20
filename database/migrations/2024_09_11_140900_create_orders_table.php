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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->decimal("total", 8, 2)->default(0.00);
            $table->string("notes")->nullable();
            // $table->json("products");
            $table->enum("status", ["1", "0"])->default("1");
            $table->enum("payment_type", ["cashed", "online", "unpaid"])->default('unpaid');
            $table->enum("payment_status", ["paid", "pending", "unpaid"])->default('unpaid');
            $table->enum("order_status", ["in_progress", "cancelled", "done"])->default('in_progress');
            $table->bigInteger("transaction_id")->nullable();
            $table->foreignId("employee_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("customer_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId("dining_table_id")->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
