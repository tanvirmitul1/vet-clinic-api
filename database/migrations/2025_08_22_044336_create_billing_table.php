<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingTable extends Migration
{
   public function up()
{
    if (Schema::hasTable('appointments') && Schema::hasTable('owners')) {
        Schema::create('billing', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('appointment_id');
            $table->unsignedBigInteger('owner_id');
            $table->string('invoice_number')->unique();
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->enum('status', ['pending','paid','overdue','cancelled'])->default('pending');
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            $table->string('payment_method')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('appointment_id')->references('id')->on('appointments');
            $table->foreign('owner_id')->references('id')->on('owners');
        });
    }
}

    public function down()
    {
        Schema::dropIfExists('billing');
    }
}