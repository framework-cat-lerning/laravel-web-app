<?php

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('consumption_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Product::class)->nullable(false)->comment("商品ID")->constrained();

            $table->timestamp("consumption_at")->nullable(false)->comment("購入日時");
            $table->unsignedInteger("quantity")->nullable(false)->comment("購入数");
            $table->foreignIdFor(User::class)->nullable(false)->comment("購入者ユーザ")->constrained();

            $table->timestamps();
            $table->softDeletes();
            $table->comment("購履歴ログ");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('consumption_logs');
    }
};
