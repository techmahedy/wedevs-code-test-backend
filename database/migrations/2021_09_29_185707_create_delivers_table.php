<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeliversTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('delivers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')
                    ->index()
                    ->constrained()
                    ->onDelete('cascade');
            $table->foreignId('user_id')
                    ->index()
                    ->constrained()
                    ->onDelete('cascade');
            $table->foreignId('product_id')
                    ->index()
                    ->constrained()
                    ->onDelete('cascade');
            $table->integer('qty');
            $table->integer('price');
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('delivers');
    }
}
