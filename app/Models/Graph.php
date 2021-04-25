<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Graph extends Model
{
    // use HasFactory;

    protected $guarded = ['id'];

    public function nodes()
    {
        return $this->hasMany(GraphNode::class, 'graph_id', 'id');
    }

    public function get_top_level_subgraphs()
    {
        $ids = [];
        $subgraphs = collect([]);
        foreach ($this->nodes()->get() as $node)
        {
            if (isset($node->options['graph_id']))
            {
                if (isset($ids[$node->options['graph_id']]))
                    continue;
                if ($node->options['graph_id'] == $this->id)
                    continue;
                $ids[$node->options['graph_id']] = 1;

                $subgraph = Graph::find($node->options['graph_id']);
                $subgraphs->push($subgraph);
            }
        }

        return $subgraphs;
    }

    /**
     * Returns the current Graph as Array. If the recursive parameter is true,
     * the main graph and first child graphs will be returned in parallel
     * @param  boolean $recursive            [description]
     * @param  array   $recursive_ids_parsed [description]
     * @return [type]                        [description]
     */
    public function exportSerialize($recursive = true, $recursion_level = 0, $result = null)
    {

        if ($recursion_level > 20)
        {
            throw new \Exception("Max recursion level has been reached");
        }

        if ($result === null)
        {
            $result = collect();
        }
        elseif ($result->has($this->id))
        {
            return null;
        }

        $this_children_ids = [];

        $this_array = collect([
            'id' => $this->id,
            'is_root' => (!$recursive || $recursion_level === 0 ? true : false),
            'name' => $this->name,
            'nodes' => collect(),
            'edges' => collect(),
        ]);

        $edges = collect();

        foreach($this->nodes()->get() as $node)
        {
            $node_options = $node->options;

            if (!$recursive && isset($node_options['graph_id']))
            {
                unset($node_options['graph_id']);
            }

            $this_array->get('nodes')->push([
                'id' => $node->id,
                'node_type_id' => $node->node_type_id,
                'content' => $node->content,
                'options' => $node_options,
            ]);

            if (isset($node->options['graph_id']))
                $this_children_ids[] = $node->options['graph_id'];

            $node_edges_out = $node->edges_out()->get();

            $edges = $edges->merge($node_edges_out);
        }

        foreach($edges as $edge)
        {
            $this_array->get('edges')->push([
                'id' => $node->id,
                'node_from_id' => $edge->node_from_id,
                'node_to_id' => $edge->node_to_id,
                'content' => $edge->content,
            ]);
        }

        $result->put($this->id, $this_array);

        if ($recursive)
        {
            foreach(array_unique($this_children_ids) as $child_id)
            {
                if (!is_numeric($child_id))
                {
                    continue;
                }

                // Recursive call
                $sub_result = Graph::find($child_id)->exportSerialize(true, ($recursion_level + 1), $result);
                // dump($sub_result);
                if ($sub_result !== null)
                {
                    $result = $sub_result;
                }
            }
        }

        return $result;

    }

}
