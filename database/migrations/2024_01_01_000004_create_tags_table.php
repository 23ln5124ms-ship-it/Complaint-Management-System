<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique();
            $table->string('color', 7)->default('#94a3b8');
            $table->timestamps();
        });

        Schema::create('complaint_tag', function (Blueprint $table) {
            $table->id();
            $table->foreignId('complaint_id')->constrained()->onDelete('cascade');
            $table->foreignId('tag_id')->constrained()->onDelete('cascade');
            $table->unique(['complaint_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('complaint_tag');
        Schema::dropIfExists('tags');
    }
};
