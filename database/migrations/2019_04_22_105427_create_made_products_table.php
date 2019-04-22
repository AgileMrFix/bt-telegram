<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMadeProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('made_products', function (Blueprint $table) {
            $table->integer('string_number')->index();
            $table->string('production_report_number')->index();
            $table->string('name');
            $table->string('option_uid');
            $table->string('option');
            $table->string('amount');
            $table->string('weight');
            $table->timestamps();

            $table->unique(['string_number', 'production_report_number']);
        });


    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('made_products');
    }
}
