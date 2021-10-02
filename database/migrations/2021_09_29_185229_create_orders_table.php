<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
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
            $table->enum('status',['pending', 'approved', 'reject', 'processing', 'shipped', 'deliverd'])->default('pending');
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
        Schema::dropIfExists('orders');
    }
}
