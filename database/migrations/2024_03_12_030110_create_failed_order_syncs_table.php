<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('failed_order_syncs', function (Blueprint $table) {
            $table->id();
            $table->json('order_data');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('failed_order_syncs');
    }
};
