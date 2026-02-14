<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('estimates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->text('brief');
            $table->json('result'); // Full JSON result
            $table->string('provider'); // Which AI was used
            $table->string('model');
            $table->decimal('total_hours', 8, 2);
            $table->decimal('price_low', 10, 2);
            $table->decimal('price_high', 10, 2);
            $table->string('currency', 3);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('estimates');
    }
};
