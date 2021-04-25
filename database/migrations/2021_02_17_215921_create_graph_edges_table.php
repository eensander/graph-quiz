<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGraphEdgesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('graph_edges', function (Blueprint $table) {
            $table->id();

            $table->foreignId('node_from_id');
            $table->foreignId('node_to_id');

            // if content null, regular connection is assumed
            // if content is not null, edge will be seen as flowline/decision-option and content will be option
            $table->string('content')->nullable();

            // equivalent tot node's options
            $table->string('options')->default(json_encode([]));

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
        Schema::dropIfExists('graph_edges');
    }
}
