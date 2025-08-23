<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAppointmentsTable extends Migration
{
 public function up()
{
    if (Schema::hasTable('pets') && Schema::hasTable('users')) {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pet_id');
            $table->unsignedBigInteger('doctor_id');
            $table->dateTime('appointment_date');
            $table->enum('status', ['scheduled','confirmed','in_progress','completed','cancelled'])->default('scheduled');
            $table->string('reason');
            $table->text('notes')->nullable();
            $table->decimal('estimated_cost', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('pet_id')->references('id')->on('pets');
            $table->foreign('doctor_id')->references('id')->on('users');
        });
    }
}



    public function down()
    {
        Schema::dropIfExists('appointments');
    }
}