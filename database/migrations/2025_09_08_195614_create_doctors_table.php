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
        Schema::create('doctors', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('specialty');
            $table->string('phone')->nullable();
            $table->string('address');
            $table->time('work_from');
            $table->time('work_to');
            $table->boolean('active')->default(false);
            $table->string('image')->nullable();
            $table->string('bio')->nullable();
            $table->string('license')->nullable();
            $table->string('degree')->nullable();
            $table->unsignedSmallInteger('ticket_price');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('doctors');
    }
};
