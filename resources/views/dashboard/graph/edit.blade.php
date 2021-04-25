@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-lg text-gray-800 leading-tight">
        {{ __('Graph Modeler') }}
    </h2>
@endsection

@section('page_content')
    <div class="py-6" id="app">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 bg-white border-b border-gray-200">
                    <h2 class="text-lg">Editing the graph: {{ $graph->name }}</h2>
                </div>

                <graph-editor-component></graph-editor-component>

            </div>

        </div>
    </div>
@endsection
