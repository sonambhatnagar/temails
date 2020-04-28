<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

/**
 * Class CreateEmailStatusTable
 */
class CreateEmailStatusTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('email_status', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->text('request_data');
            $table->text('response_data');
            $table->string('status', 30)->default('Not Sent');
            $table->index(['status', 'id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('email_status');
    }
}
