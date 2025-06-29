<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChratofaccountTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('chartofaccounts', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('business_id');
            $table->string('name')->nullable();
            $table->string('code')->nullable();
            $table->integer('parent_id')->nullable()->default(0);
            $table->integer('haschild')->default(0);
            $table->integer('cost_center_id')->default(0);
            $table->integer('is_admin')->default(0);
            $table->integer('type')->default(0);
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
        Schema::dropIfExists('chartofaccounts');
    }
}
