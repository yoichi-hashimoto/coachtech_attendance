<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChangeRestsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('change_rests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rest_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('change_attendance_id')->constrained()->onDelete('cascade');
            $table->time('requested_break_start_time')->nullable();
            $table->time('requested_break_end_time')->nullable();
            $table->enum('approval_status',['承認待ち','承認済み']);
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
        Schema::dropIfExists('change_rests');
    }
}
