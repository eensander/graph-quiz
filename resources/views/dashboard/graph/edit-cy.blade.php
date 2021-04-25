@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Graph Modeler') }}
    </h2>
@endsection

@section('page_content')
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg" x-data xxx-data="graphEditor()" xxx-init="init($watch)">

                <div class="p-6 bg-white border-b border-gray-200">

                    <h2 class="text-lg">Test Modeler</h2>

{{--
                    <div class="flex flex-row mt-2 border border-gray-200 rounded">
                        <div class="w-2/3 border-r border-gray-200 p-4 flex flex-col">
                            <span class="text-gray-900 w-full border-b border-gray-200 pb-1 mb-3">Nodes</span>

                            <template x-for="[node_i, node] of Object.entries($store.graph.nodes)" :key="node_i">
                                <div x-init="console.log(node)">
                                    <div class="w-full p-2 bg-gray-100 hover:bg-gray-200 cursor-pointer border-b border-gray-300" @click="node.open = !node.open ?? false">
                                        <span x-text="node_i"></span>
                                        <span @click="console.log(node.edges_in)"  x-text="node.type"></span>
                                    </div>
                                    <div x-show="node.open ?? false == true" class="py-2">

                                        <span class="font-bold text-gray-800">Content:</span><br />
                                        <span class="text-sm" x-text="node.content"></span>

                                        <div class="flex">
                                            <div class="w-1/2 pr-1">
                                                {{-- <table class="w-1/2 mr-1 flex-initial"> --.}}
                                                <table class="">
                                                    <tbody>
                                                        <tr>
                                                            <th colspan="2">
                                                                INPUTS
                                                            </th>
                                                        </tr>
                                                        <template x-if="Object.keys(node.edges_in).length > 0">
                                                            <template x-for="[edge_i, edge] of Object.entries(node.edges_in)">
                                                                <tr>
                                                                    <td x-text="'Node-id ' + edge.node_from_id"></td>
                                                                    <td class="cursor-pointer">Go to node (TODO)</td>
                                                                </tr>
                                                            </template>
                                                        </template>
                                                        <template x-if="!Object.keys(node.edges_in).length > 0">
                                                            <tr>
                                                                <td>No inputs</td>
                                                            </tr>
                                                        </template>
                                                    </tbody>
                                                </table>
                                            </div>

                                            <div class="w-1/2 pl-1">
                                                    {{-- <table class="w-1/2 ml-1 flex-initial"> --.}}
                                                    <table class="">
                                                        <tbody>
                                                            <tr>
                                                                <th colspan="2">
                                                                    OUTPUTS
                                                                </th>
                                                            </tr>
                                                            <template x-if="Object.keys(node.edges_in).length > 0">
                                                                <template x-for="[edge_i, edge] of Object.entries(node.edges_out)">
                                                                    <tr>
                                                                        <td x-text="'Node-id ' + edge.node_from_id"></td>
                                                                        <td class="cursor-pointer">Go to node (TODO)</td>
                                                                    </tr>
                                                                </template>
                                                            </template>
                                                            <template x-if="!Object.keys(node.edges_out).length > 0">
                                                                <tr>
                                                                    <td>No outputs</td>
                                                                </tr>
                                                            </template>
                                                        </tbody>
                                                    </table>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </template>
                        </div>
                        <div class="w-1/3 p-4 flex flex-col">
                            <span class="text-gray-900 w-full border-b border-gray-200 pb-1 ">Edges</span>

                            {{-- <template x-for="edge of edges">
                                Gang
                                <span x-text="edge.name"></span>
                            </template> --.}}

                        </div>
                    </div>

                    --}}

                </div>

                <div class="p-6 bg-white border-b border-gray-200" x-data="graphModeler()" x-init="init()">
                    Test GRAPH Modeler

                    <input type="button" @click="reloadModeler()" value="reload" />

<!--
                    <div class="flex flex-row">
                        <div class="flex flex-col w-24 border-gray-300 border">
                            <div class=" w-full h-16 border-b drag-drawflow" draggable="true" x-on:dragstart="drag(event)" data-node-type="start">
                                <svg width="100%" viewBox="-48 -32 96 64">
                                    <rect x="-36" y="-20" width="72" height="40" rx="15" style="fill: #d9d9d9; stroke: #a0a0a0; stroke-width: 2px;"></rect>
                                    <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" font-size="1rem" fill="black">Start</text>
                                </svg>
                            </div>
                            <div class="w-full h-16 border-b drag-drawflow" draggable="true" x-on:dragstart="drag(event)" data-node-type="end">
                                <svg width="100%" viewBox="-48 -32 96 64">
                                    <rect x="-36" y="-20" width="72" height="40" rx="15" style="fill: #ccc; stroke: black; stroke-width: 1px;"></rect>
                                    <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" font-size="1rem" fill="black">End</text>
                                </svg>
                            </div>
                            <div class="w-full h-16 border-b drag-drawflow" draggable="true" x-on:dragstart="drag(event)" data-node-type="notice">
                                <svg width="100%" viewBox="-48 -32 96 64">
                                    {{-- <path d="M 0 -19.2 L 38.4 -19.2 L 38.4 19.2 L -38.4 19.2 L -38.4 -19.2 Z" style="stroke: black; stroke-width: 1px; fill: #ccc;"></path> --}}
                                    <rect x="-35" y="-20" width="70" height="40"  style="stroke: black; stroke-width: 1px; fill: #ccc;"></rect>
                                    <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" font-size="1rem" fill="black">Notice</text>
                                </svg>
                            </div>
                            <div class="w-full h-20 border-b drag-drawflow" draggable="true" x-on:dragstart="drag(event)" data-node-type="start">
                                <svg width="100%" viewBox="-45 -40 90 80">
                                    <path d="M 0 -32 L 32 0 L 0 32 L -32 0 L 0 -32" style="stroke: black; stroke-width: 1px; fill: #ccc;"></path>
                                    <text x="0" y="0" dominant-baseline="middle" text-anchor="middle" font-size="0.9rem" fill="black">Decision</text>
                                </svg>
                            </div>
                        </div>
-->

                        <div class="w-full relative" style="height: 36rem;" id="modeler-canvas" x-on:dragover="return false" x-on:drop="drop(event)"></div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection

@section('body_tail')
<script>
/*
// https://web.dev/drag-and-drop/
document.addEventListener('DOMContentLoaded', (event) => {

  function handleDragStart(e) {
    this.style.opacity = '0.9';
    // alert('gang');
  }

  function handleDragEnd(e) {
    this.style.opacity = '1';
  }

  let items = document.querySelectorAll('.drag-drawflow');
  items.forEach(function(item) {
    item.addEventListener('dragstart', handleDragStart, false);
    item.addEventListener('dragend', handleDragEnd, false);
  });
});
*/
</script>
@endsection
