<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForeignKeys extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('graphs', function (Blueprint $table) {
            $table->foreign('user_id')->references('id')->on('users')
                // ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('graph_nodes', function (Blueprint $table) {
            $table->foreign('graph_id')->references('id')->on('graphs')
                // ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('node_type_id')->references('id')->on('graph_node_types')
                // ->onUpdate('cascade')
                ->onDelete('cascade');
        });

        Schema::table('graph_edges', function (Blueprint $table) {
            $table->foreign('node_from_id')->references('id')->on('graph_nodes')
                // ->onUpdate('cascade')
                ->onDelete('cascade');

            $table->foreign('node_to_id')->references('id')->on('graph_nodes')
                // ->onUpdate('cascade')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
