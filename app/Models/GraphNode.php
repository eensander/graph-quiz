<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraphNode extends Model
{
    protected $casts = [
        'options' => 'array',
    ];

    protected $guarded = ['id'];

    public function graph()
    {
        return $this->belongsTo(Graph::class, 'id', 'graph_id');
    }

    public function node_type()
    {
        return $this->hasOne(GraphNodeType::class, 'id', 'node_type_id');
    }

    // edges incoming in this node
    public function edges_in()
    {
        return $this->hasMany(GraphEdge::class, 'node_to_id');
    }

    // edges outgoing from this node
    public function edges_out()
    {
        return $this->hasMany(GraphEdge::class, 'node_from_id');
    }

    // 'out', from $this to $target
    // returns: $this, so method chaining is possible
    public function connect_node_out(GraphNode $target_node, $edge_content = null)
    {
        GraphEdge::create([
            'node_from_id' => $this->id,
            'node_to_id'   => $target_node->id,
            'content'      => $edge_content
        ]);

        return $this;
    }

    // 'in', from $target to $this
    // returns: $this, so method chaining is possible
    public function connect_node_in(GraphNode $target_node, $edge_content = null)
    {
        GraphEdge::create([
            'node_from_id' => $target_node->id,
            'node_to_id'   => $this->id,
            'content'      => $edge_content
        ]);

        return $this;
    }


    /**
     * @param  array $options options to be checked
     * @return array $validator
     */
    public static function validate_options($options) {

        // https://laravel.com/docs/8.x/validation#validating-arrays
        $rules = [
            'graph_id' => [
                'required',
                // https://laravel.com/docs/8.x/validation#specifying-a-custom-column-name
                'exists' => 'graphs,id',
            ],
            'conditions' => [],
            'annotation' => ['string'],
        ];

        $validator = Validator::make($options, $rules);
        return $validator;

    }

}
