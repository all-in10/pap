<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('timeoff_categories', function (Blueprint $table) {
            $table->id(); // id é unsignedBigInteger por padrão
            $table->string('key')->unique();
            $table->string('label');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('timeoff_categories');
    }
};
