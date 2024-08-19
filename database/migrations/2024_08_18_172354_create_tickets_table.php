<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number');
            $table->unsignedBigInteger('created_by');
            $table->unsignedBigInteger('closed_by')->nullable();
            $table->enum('location', ['HQ', 'NTPS', 'LTPS', 'LKHEP', 'KLHEP', 'Longku', 'Narengi', 'Jagiroad']);
            $table->enum('subject', ['Desktop', 'Keyboard', 'Laptop', 'Monitor', 'Mouse', 'Network', 'Office', 'Touchpad', 'UPS', 'Other']);
            $table->string('serial_num')->nullable();
            $table->string('description');
            $table->enum('call_type', ['Demo', 'Installation', 'Service'])->nullable();
            $table->dateTime('sla_overdue');
            $table->enum('status', ['Open', 'Closed']);
            $table->string('remarks')->nullable();
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            // Adding foreign key constraints
            $table->foreign('created_by')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('closed_by')->references('id')->on('users')->onDelete('set null'); // You can choose set null or cascade
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
