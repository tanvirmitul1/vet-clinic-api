<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePetsTable extends Migration
{
    public function up()
    {
        Schema::create('pets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('species');
            $table->string('breed')->nullable();
            $table->string('color')->nullable();
            $table->date('birth_date')->nullable();
            $table->enum('gender', ['male', 'female']);
            $table->decimal('weight', 5, 2)->nullable();
            $table->text('medical_history')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('owner_id');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            
            $table->foreign('owner_id')->references('id')->on('owners')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pets');
    }
}