<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Use the analytics connection explicitly
        Schema::connection('analytics')->create('page_views', function (Blueprint $table) {
            $table->id();
            $table->string('page', 191);
            $table->integer('count')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::connection('analytics')->dropIfExists('page_views');
    }
};
