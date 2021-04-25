<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

use App\Models\Graph;
use App\Models\GraphNode;
use App\Models\GraphNodeType;
use App\Models\GraphEdge;

class TestDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $this->test_user = \App\Models\User::create([
            'name' => 'test',
            'email' => 'test@test.com',
            'password' => Hash::make('test'),
        ]);

        // node types
        $this->node_type_start = GraphNodeType::where(['name' => 'start'])->first();

        $this->node_type_end = GraphNodeType::where(['name' => 'end'])->first();

        $this->node_type_decision = GraphNodeType::where(['name' => 'decision'])->first();

        $this->node_type_notice = GraphNodeType::where(['name' => 'notice'])->first();

        $graph_lawful_access_a = $this->graph_lawful_access_a();

        $this->graph_delivered_example_is_tdm_allowed($graph_lawful_access_a);

    }

    protected function graph_lawful_access_a()
    {

        // graphs
        $graph = Graph::create([
            'name' => '[A] Is the user a lawful user / does the user have lawful access?',
            'user_id' => $this->test_user->id
        ]);

        // edges
        $node_end_yes =  $graph->nodes()->create([
            'node_type_id' => $this->node_type_end->id,
            'content' => 'Yes'
        ]);
        $node_end_no = $graph->nodes()->create([
            'node_type_id' => $this->node_type_end->id,
            'content' => 'No'
        ]);

        $graph->nodes()->create([
            'node_type_id' => $this->node_type_start->id,
            'content' => '[A] Is there lawful / does the user have lawful access?',
        ])->connect_node_out(

            $graph->nodes()->create([
                'node_type_id' => $this->node_type_decision->id,
                'content' => "Is there 'open access' or a open access license?",
            ])
                ->connect_node_out(
                    $node_end_yes,
                    "Yes"
                )->connect_node_out(
                    $graph->nodes()->create([
                        'node_type_id' => $this->node_type_decision->id,
                        'content' => "Are there contractual agreements between rightholder and beneficiary?",
                    ])
                        ->connect_node_out(
                            $node_end_yes,
                            "Yes"
                        )->connect_node_out(
                            $graph->nodes()->create([
                                'node_type_id' => $this->node_type_decision->id,
                                'content' => "Are other lawful means applicable?",
                                'options' => [
                                    "annotation" => 'Reference Recitals 10 & D 2019/790'
                                ]
                            ])
                                ->connect_node_out(
                                    $node_end_yes,
                                    "Yes"
                                )->connect_node_out(
                                    $node_end_no,
                                    "No"
                                ),
                            "No"
                        ),
                    "No"
                )

        );

        return $graph;

    }

    protected function graph_delivered_example_is_tdm_allowed($graph_lawful_access_a)
    {

        // graphs
        $graph = Graph::create([
            'name' => 'Is TDM allowed?',
            'user_id' => $this->test_user->id
        ]);

        // edges
        $node_end_allowed =  $graph->nodes()->create([
            'node_type_id' => $this->node_type_end->id,
            'content' => 'Data extraction and reutilization is allowed',
            'options' => [
                'appearance' => ['x' => 60, 'y' => 380],
            ]
        ]);
        $node_end_not_allowed = $graph->nodes()->create([
            'node_type_id' => $this->node_type_end->id,
            'content' => 'TDM is not allowed',
            'options' => [
                'appearance' => ['x' => 290, 'y' => 380],
            ]
        ]);

        $node_dec_is_lawful_user = $graph->nodes()->create([
            'node_type_id' => $this->node_type_decision->id,
            'content' => 'Is the user a lawful user / does the user have lawful access?',
            'options' => [
                'annotation' => "Lawful access may include:\n - Open Access,\n - Contractual agreement,\n - etc...",
                'graph_id' => $graph_lawful_access_a->id,
                'appearance' => ['x' => 310, 'y' => 240],
            ]
        ])
            ->connect_node_out(
                $graph->nodes()->create([
                    'node_type_id' => $this->node_type_notice->id,
                    'content' => "[ Path for 'Yes' should continue here ]",
                    'options' => [
                        'appearance' => ['x' => 470, 'y' => 380],
                    ]
                ]),
                "Yes"
            )
            ->connect_node_out(
                $node_end_not_allowed,
                "No"
            );

        $graph->nodes()->create([
            'node_type_id' => $this->node_type_start->id,
            'content' => 'Is TDM allowed?',
            'options' => [
                'appearance' => ['x' => 170, 'y' => 40],
            ]
        ])->connect_node_out(

            $graph->nodes()->create([
                'node_type_id' => $this->node_type_decision->id,
                'content' => 'Has the data been made available to the public?',
                'options' => [
                    'annotation' => "The scope of Directive 1996/9 EC: the legal protection of databases in any form\nArt. 1 D 1996/9/EC",
                    'appearance' => ['x' => 190, 'y' => 130],
                ]
            ])
                ->connect_node_out(
                    $node_dec_is_lawful_user,
                    "Yes"
                )->connect_node_out(
                    $graph->nodes()->create([
                        'node_type_id' => $this->node_type_decision->id,
                        'content' => 'Is it a non-electronic database',
                        'options' => [
                            'appearance' => ['x' => 80, 'y' => 240],
                        ]
                    ])
                        ->connect_node_out(
                            $node_end_allowed,
                            "Yes"
                        )
                        ->connect_node_out(
                            $node_dec_is_lawful_user,
                            "No"
                        ),
                    "No"
                )

        );

        return $graph;

    }


}
