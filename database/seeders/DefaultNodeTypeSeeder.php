<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\Graph;
use App\Models\GraphNode;
use App\Models\GraphNodeType;
use App\Models\GraphEdge;

class DefaultNodeTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        // node types
        $this->node_type_start = GraphNodeType::create([
            'name' => 'start',
        ]);

        $this->node_type_end = GraphNodeType::create([
            'name' => 'end',
        ]);

        $this->node_type_notice = GraphNodeType::create([
            'name' => 'notice',
        ]);

        $this->node_type_decision = GraphNodeType::create([
            'name' => 'decision',
        ]);
    }

}
