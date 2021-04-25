@extends('layouts.guest')

@php
use App\Models\Graph;
@endphp

@section('page_content')
    <div class="my-16 mx-auto max-w-6xl">

        <h2>Available Graphs:</h2>

        <table>
            <thead>
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        Total Nodes
                    </th>
                    <th>
                        End nodes
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach (Graph::all() as $graph)
                    <tr>
                        <td><a href="{{ route('graph.show', $graph) }}">{{ $graph->name }}</a></td>
                        <td>{{ $graph->nodes()->count() }}</td>
                        <td>{{ $graph->nodes()->join('graph_node_types', 'graph_nodes.node_type_id', '=', 'graph_node_types.id')->where('graph_node_types.name', 'end')->count() }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

    </div>
@endsection
