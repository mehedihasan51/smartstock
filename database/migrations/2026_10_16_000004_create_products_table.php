<?php
use Illuminate\Database\Migrations\Migration; use Illuminate\Database\Schema\Blueprint; use Illuminate\Support\Facades\Schema;
return new class extends Migration { public function up(): void {
    Schema::create('products',function(Blueprint $t){
        $t->id();$t->string('name');
        $t->string('sku')->unique();
        $t->foreignId('category_id')->constrained()->restrictOnDelete();
        $t->foreignId('supplier_id')->nullable()->constrained()->nullOnDelete();
        $t->decimal('unit_price',12,2);
        $t->unsignedInteger('stock_quantity')->default(0);
        $t->unsignedInteger('reorder_level')->default(5);
        $t->timestamps();});} public function down(): void {
            Schema::dropIfExists('products');}};
