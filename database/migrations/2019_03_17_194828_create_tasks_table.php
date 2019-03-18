<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTasksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       Schema::create('tasks_types', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name')->unique();
          $table->timestamps();
       });

       Schema::create('dates', function (Blueprint $table) {
          $table->bigIncrements('id');
          $table->string('name')->nullable();
          $table->date('date');
          $table->bigInteger('user_id')->unsigned();
          $table->timestamps();
       });

        Schema::create('tasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('description')->nullable();
            $table->dateTime('execute_time')->nullable();
            $table->bigInteger('date_id')->unsigned()->nullable();
            $table->bigInteger('type_id')->unsigned()->nullable();
            $table->timestamps();
        });

        Schema::table('tasks', function  (Blueprint $table) {
          $table->foreign('date_id')->references('id')->on('dates')->onUpdate('cascade')->onDelete('cascade');
          $table->foreign('type_id')->references('id')->on('tasks_types')->onUpdate('cascade')->onDelete('set null');
          $table->unique(['name', 'date_id']);
        });

        Schema::table('dates', function  (Blueprint $table) {
          $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      Schema::dropIfExists('tasks');
      Schema::dropIfExists('tasks_types');
      Schema::dropIfExists('dates');
    }
}
