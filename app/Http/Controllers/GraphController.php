<?php

namespace App\Http\Controllers;

use App\Models\Graph;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

use App\Models\GraphEdge;
use App\Models\GraphNode;
use App\Models\GraphNodeType;

class GraphController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $graphs = Graph::where('user_id', auth()->user()->id)->get();

        return view('dashboard.graph.index', compact('graphs'));
    }

    public function index_json()
    {
        $graphs = Graph::where('user_id', auth()->user()->id)->get();

        $graphs->map(function($x) {
            $x->amount_nodes = $x->nodes()->count();
            $x->subgraphs = $x->get_top_level_subgraphs()->map(function($x) {
                return $x->only(['id', 'name']);
            });
        });

        return compact('graphs');
    }

    /**
     * Return the graph as json object
     *
     * @return \Illuminate\Http\Response
     */
    public function export(Request $request, Graph $graph)
    {
        if ($request->get('include_subgraphs'))
        {
            // $content = json_encode($graph->toArrayRecursive(0));
            $content = $graph->exportSerialize(true)->toJSON();
        }
        else
        {
            $content = $graph->exportSerialize(false)->toJSON();
        }


        // https://stackoverflow.com/a/41434119
        // alternative solution for 8.x: https://laravel.com/docs/8.x/responses#streamed-downloads
        $headers = [
            'Content-type'        => 'text/json',
            'Content-Disposition' => 'attachment; filename="export-graph-'.date('Ymd-His').'.json"',
            'Content-Length'      => strlen($content),
        ];

        return \Response::make($content, 200, $headers);

    }

    /**
     * Inserts one or more graphs from an array
     *
     * @return \Illuminate\Http\Response
     */
    public function import(Request $request)
    {

        // TODO: define more globally
        $validated = $request->validate([

            'import_data' => 'array',
            'import_data.*.id' => 'required|numeric',
            'import_data.*.name' => 'required|string|min:2|max:255',

            'import_data.*.nodes' => 'required|array',
            'import_data.*.nodes.*.id' => 'required|numeric',
            'import_data.*.nodes.*.node_type_id' => 'required|exists:graph_node_types,id',
            'import_data.*.nodes.*.content' => 'required|string',

            'import_data.*.nodes.*.options' => 'array',
            'import_data.*.nodes.*.options.annotation' => 'string',
            'import_data.*.nodes.*.options.graph_id' => 'numeric',

            'import_data.*.edges' => 'required|array',
            'import_data.*.edges.*.id' => 'required|numeric',
            'import_data.*.edges.*.node_from_id' => 'required|numeric',
            'import_data.*.edges.*.node_to_id' => 'required|numeric',
            'import_data.*.edges.*.content' => 'nullable|string',

        ]);

        $root_graph_id = null;

        $id_map = [
            'graph' => [],
            'node' => [],
            // 'edge' => [],
        ];

        foreach($validated['import_data'] as $graph)
        {
            $mod_graph = Graph::create([
                'name' => $graph['name'],
                'user_id' => auth()->user()->id,
            ]);

            $id_map['graph'][$graph['id']] = $mod_graph;

            if (isset($graph['is_root']) && $graph['is_root'] == true)
                $root_graph_id = $mod_graph->id;

            foreach($graph['nodes'] as $node)
            {
                $mod_node = $mod_graph->nodes()->create([
                    'node_type_id' => $node['node_type_id'],
                    'content' => $node['content'],
                    'options' => $node['options'] ?? null,
                ]);

                $id_map['node'][$node['id']] = $mod_node;
            }

            foreach($graph['edges'] as $edge)
            {
                if (!isset($id_map['node'][$edge['node_from_id']]))
                    return response()->json(['message' => 'Edge with invalid input-node given (id: ' . $edge['id'] . ')']);

                if (!isset($id_map['node'][$edge['node_to_id']]))
                    return response()->json(['message' => 'Edge with invalid output-node given (id: ' . $edge['id'] . ')']);

                $mod_node = GraphEdge::create([
                    'node_from_id' => $id_map['node'][$edge['node_from_id']]->id,
                    'node_to_id' => $id_map['node'][$edge['node_to_id']]->id,
                    'content' => $edge['content'] ?? null,
                ]);

                // $id_map['node'][$node['id']] = $mod_node->id;
            }
        }

        // perform checking for subgraphs AFTER all (relevant) graphs have been imported
        $subgraphs_discarded = 0;
        foreach($id_map['node'] as $json_id => $node)
        {
            if (isset($node->options['graph_id']))
            {
                if (isset($id_map['graph'][$node->options['graph_id']]))
                {
                    $options = $node->options;
                    $options['graph_id'] = $id_map['graph'][$node->options['graph_id']]->id;
                    $node->options = $options;
                    $node->save();
                }
                else
                {
                    $subgraphs_discarded += 1;
                }
            }
        }

        return response()->json([
            'message' => 'Imported ' . count($id_map['graph']) . ' graphs successfully.' .
            ($subgraphs_discarded > 0 ? '\n' . $subgraphs_discarded . ' subgraphs were not provided and have been discarded.' : ''),
            'root_graph_id' => $root_graph_id,
        ]);

    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('dashboard.graph.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'string|between:2,255',
            // 'description' => 'string|between:2,255',
        ]);

        // dd($validated);

        $graph = new Graph;
        $graph->name = $validated['name'];
        $graph->user_id = auth()->user()->id;
        $graph->save();

        return redirect()->route('dashboard.graph.edit', $graph);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Graph  $graph
     * @return \Illuminate\Http\Response
     */
    public function show(Graph $graph)
    {
        return view('public.graph.show', compact('graph'));
    }

    public function get(Graph $graph)
    {
        // sleep(1);
        // encoding to edges and nodes array

        $return = [
            'graph' => [
                'id' => $graph->id,
                'name' => $graph->name,
            ],
            'nodes' => [],
            'edges' => [],
        ];

        foreach($graph->nodes()->get() as $node_obj)
        {
            $node = [
                'id' => $node_obj->id,
                'type' => $node_obj->node_type()->first()->name,
                'content' => $node_obj->content,
                'options' => $node_obj->options,
                // 'dynamic' => [],
                // 'edges_in' => [],
                'edges_out' => [],
            ];

            foreach(['edges_out'] as $edge_direction)
            {
                foreach($node_obj->$edge_direction()->get() as $edge_obj)
                {
                    $edge = [
                        'id' => $edge_obj->id,
                        'node_from_id' => $edge_obj->node_from_id,
                        'node_to_id' => $edge_obj->node_to_id,
                        'content' => $edge_obj->content
                    ];

                    $node[$edge_direction][$edge['id']] = $edge;
                    $return['edges'][$edge['id']] = $edge;

                }
            }

            $return['nodes'][$node['id']] = $node;

        }

        return $return;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Graph  $graph
     * @return \Illuminate\Http\Response
     */
    public function edit(Graph $graph)
    {
        return view('dashboard.graph.edit', compact('graph'));
    }

    /**
     * Update the specified resource in storage.
     * Very similar to import() function. Could maybe be merged later.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Graph  $graph
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Graph $graph)
    {
        // return ['message' => ['text' => 'Execution prevented', 'type' => 'info']];

        $validated = $request->validate([

            'graph' => 'nullable',
            'graph.name' => 'string',

            'nodes' => 'nullable|array',
            'nodes.*.content' => 'nullable|string',
            'nodes.*.node_type' => 'required|string',
            'nodes.*.options' => 'array',
            'nodes.*.options.annotation' => 'string',
            'nodes.*.options.graph_id' => 'numeric',

            'edges' => 'required|array',
            'edges.*.id' => 'required|numeric',
            'edges.*.node_from_id' => 'required|string',
            'edges.*.node_to_id' => 'required|string',
            'edges.*.content' => 'nullable|string',

        ]);

        $messages_end = [];

        if (isset($validated['graph']))
        {
            return ['TODO GRAPH'];

            $messages_end[] = ['text' => 'Updated graph information', 'type' => 'success'];
        }

        // NOTE: when updating nodes/edges, the complete collection of these have to be supplied,
        // since first all nodes/edges will get removed.

        if (isset($validated['nodes']) && isset($validated['edges']))
        {
            $delete = $graph->nodes()->delete();

            $node_types = GraphNodeType::all();
            // dump("node_types", $node_types);

            $id_map_nodes = [];

            foreach($validated['nodes'] as $raw_node)
            {
                $raw_node_options = [];
                foreach($raw_node['options'] as $raw_node_option_k => $raw_node_option_v)
                {
                    if (!in_array($raw_node_option_k, ['node_id', 'node_type']))
                        $raw_node_options[$raw_node_option_k] = $raw_node_option_v;
                }

                $node_type = $node_types->first(function($item) use ($raw_node) {
                    if (!isset($raw_node['node_type']))
                        return null;
                    return $item->name == $raw_node['node_type'];
                });

                if ($node_type == null)
                {
                    $messages_end[] = ['text' => 'Undefined node-type: ' . $raw_node['node_type'] ?? 'null', 'type' => 'error'];
                    continue;
                }

                $create = $node = $graph->nodes()->create([
                    'node_type_id' => $node_type->id,
                    'content' => $raw_node['content'],
                    'options' => $raw_node_options,
                ]);

                if (!$create)
                {
                    $messages_end[] = ['text' => 'Could not persist node: ' . $raw_node['content'], 'type' => 'error'];
                }

                $id_map_nodes[$raw_node['id']] = $node;
            }

            foreach($validated['edges'] as $raw_edge)
            {
                if (!isset($id_map_nodes[$raw_edge['node_from_id']]) || !isset($id_map_nodes[$raw_edge['node_to_id']]))
                    // edge belongs to non-existing node
                    continue;

                // currently unable to check for fail of creation to method chaining on connect_node_out
                $id_map_nodes[$raw_edge['node_from_id']]
                    ->connect_node_out(
                        $id_map_nodes[$raw_edge['node_to_id']],
                        $raw_edge['content'] != '' ? $raw_edge['content'] : null
                    );
            }

            $messages_end[] = ['text' => 'Updated graph nodes', 'type' => 'success'];

        }

        return ['messages' => $messages_end];

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Graph  $graph
     * @return \Illuminate\Http\Response
     */
    public function destroy(Graph $graph)
    {
        if ($graph->user_id != auth()->user()->id)
            return ['message' => ['text' => 'This graph does not belong to you.']];

        $graph->delete();

        return ['message' => ['text' => 'Graph successfully deleted']];
    }
}
