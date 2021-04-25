<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GraphNodeType extends Model
{

    protected $casts = [
        'appearance' => 'array',
    ];


}
