<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraphEdge extends Model
{
    protected $casts = [
        'options' => 'array',
    ];

    protected $guarded = ['id'];

    public function node_from()
    {
        return $this->belongsTo(GraphNode::class, 'node_from_id', 'id');
    }

    public function node_to()
    {
        // return $this->belongsTo(GraphNode::class, 'id', 'node_to_id');
        return $this->belongsTo(GraphNode::class, 'node_to_id', 'id');
    }
}
