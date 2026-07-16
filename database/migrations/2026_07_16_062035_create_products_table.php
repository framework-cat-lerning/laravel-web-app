<?php

use App\Enums\ProductStatus;
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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable(false)->comment('商品名');
            $table->text('description')->nullable(true)->comment('商品説明');
            $table->unsignedInteger('price')->nullable(false)->default(0)->comment('参考価格');
            $table->unsignedTinyInteger('status')->nullable(false)->default(ProductStatus::PENDING->value)->comment('ステータス');

            $table->timestamps();
            $table->softDeletes();
            $table->comment('商品');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
