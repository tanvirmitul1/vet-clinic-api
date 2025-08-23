<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTreatmentsTable extends Migration
{
    public function up()
{
    if (Schema::hasTable('appointments') && Schema::hasTable('pets') && Schema::hasTable('users')) {
        Schema::create('treatments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('pet_id');
            $table->unsignedBigInteger('doctor_id');
            $table->string('diagnosis');
            $table->text('treatment_details');
            $table->text('medications')->nullable();
            $table->text('instructions')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('pet_id')->references('id')->on('pets');
            $table->foreign('doctor_id')->references('id')->on('users');
        });
    }
}

    public function down()
    {
        Schema::dropIfExists('treatments');
    }
}