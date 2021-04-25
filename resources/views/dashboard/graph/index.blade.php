@extends('layouts.app')

@section('header')
    <h2 class="font-semibold text-xl text-gray-800 leading-tight">
        {{ __('Dashboard') }}
    </h2>
@endsection

@section('page_content')

    <div class="py-12" x-data="data_index()" x-init="init">

        <template x-if="modal_import.is_open">
        	<div class="fixed z-10 inset-0 overflow-y-auto" >
        		<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        			<div class="fixed inset-0 transition-opacity" aria-hidden="true" x-on:click="modal_import.close()">
        				<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        			</div>

        			<!-- This element is to trick the browser into centering the modal contents. -->
        			<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        			<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                			<div class="sm:flex sm:items-start">
                				<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                					<!-- Heroicon name: outline/exclamation -->
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                				</div>
                				<div class="flex-grow mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                					<h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                						Import graphs
                					</h3>
                					<div class="mt-2">
                						<p class="text-sm text-gray-500">
                							Paste the data of the desired graph(s) to import below
                						</p>
                					</div>
                                    <textarea x-model="modal_import.content" class="text-xs bg-gray-100 break-all font-mono select-all resize-none h-20 mt-2 w-full px-4 py-3 leading-5 border rounded-md" placeholder="Paste the contents of the graph here"></textarea>
                                    <div class="mt-3 flex">
                                        <label class="text-sm cursor-pointer text-gray-800 bg-gray-100 hover:bg-gray-200 border border-gray-400 py-1 px-2 rounded-md">
                                            <span>Or upload a file instead</span>
                                            {{-- https://css-tricks.com/drag-and-drop-file-uploading/ --}}
                                            <input type="file" class="hidden" x-on:change="modal_import.file_changed(event)" />
                                        </label>
                                    </div>
                                    <div class="mt-3" x-show="modal_import.message.content.length > 0" >
                                        <span x-bind:class="modal_import.message.style()" x-text="modal_import.message.content"></span>
                                    </div>
                				</div>
                			</div>
                		</div>
                		<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                			<button x-bind:disabled="modal_import.is_importing" x-on:click="modal_import.import()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-bind:class="{ 'hidden': !modal_import.is_importing }" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                				Import
                			</button>
                			<button x-on:click="modal_import.close()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                			    Close
                			</button>
                		</div>
        			</div>
        		</div>
            </div>
        </template>

        <template x-if="modal_export.is_open">
        	<div class="fixed z-10 inset-0 overflow-y-auto" >
        		<div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">

        			<div class="fixed inset-0 transition-opacity" aria-hidden="true" x-on:click="modal_export.close()">
        				<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        			</div>

        			<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

        			<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                			<div class="sm:flex sm:items-start">
                				<div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-blue-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" />
                                    </svg>
                				</div>
                				<div class="flex-grow mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                					<h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-headline">
                						Export graph: <span class="text-medium" x-text="modal_export.graph.name"></span>
                					</h3>
                					<span class="block mt-2 text-sm text-gray-500">
                						Configure available options and proceed by clicking 'export'.
                					</span>
                                    <template x-if="modal_export.graph.subgraphs.length > 0">
                                        <div class="block mt-3 rounded border border-gray-300 p-3">
                                            <div class="block mb-2">
                                                <span class="block mb-2 text-sm text-gray-500">This graph includes various subgraphs. The top-level subgraphs of this graph are listed below.</span>
                                                <table class="w-full rounded text-gray-700">
                                                    <tr>
                                                        <th class="border py-1 px-2">Id</th>
                                                        <th class="border py-1 px-2">Name</th>
                                                    </tr>
                                                    <template x-for="subgraph in modal_export.graph.subgraphs">
                                                        <tr>
                                                            <td class="border py-1 px-2" x-text="subgraph.id"></td>
                                                            <td class="border py-1 px-2" x-text="subgraph.name"></td>
                                                        </tr>
                                                    </template>
                                                </table>
                                            </div>
                                            <span class="block mb-2 text-sm text-gray-500">You can choose to include these subgraphs and their subgraphs recursively (if any) in the export by enabling the checkbox below.</span>
                                            <label class="inline-flex items-center">
                                                <input x-model="modal_export.include_subgraphs" type="checkbox" class="transition duration-100 ease-in-out rounded shadow-sm focus:border-blue-500 focus:ring-2 focus:ring-blue-500 focus:outline-none focus:ring-opacity-50 focus:ring-offset-0 disabled:opacity-50 disabled:cursor-not-allowed text-blue-600 border-gray-300" checked />
                                                <span class="ml-2 text-gray-700">Include subgraphs</span>
                                            </label>
                                        </div>
                                    </template>
                                    <div class="mt-3" x-show="modal_export.message.content.length > 0" >
                                        <span x-bind:class="modal_export.message.style()" x-text="modal_export.message.content"></span>
                                    </div>
                				</div>
                			</div>
                		</div>
                		<div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                			<button x-bind:disabled="modal_export.is_exporting" x-on:click="modal_export.export()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm">
                                <svg x-bind:class="{ 'hidden': !modal_export.is_exporting }" class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                				Export
                			</button>
                			<button x-on:click="modal_export.close()" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                			    Close
                			</button>
                		</div>
        			</div>
        		</div>
            </div>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="mx-auto max-w-6xl">

                        <div class="flex justify-between items-start mt-2 mb-4 ">
                            <h2 class="text-xl flex-shrink-0">My graphs</h2>
                            <div class="w-full flex flex-row-reverse">
                                <a href="{{ route('dashboard.graph.create') }}" class="mx-2 focus:ring-2 focus:ring-offset-2 text-sm px-3 py-2 rounded-md font-semibold text-white bg-blue-500 hover:bg-blue-600">Create new Graph</a>
                                <a x-on:click="modal_import.open()" href="#" class="mx-2 focus:ring-2 focus:ring-offset-2 text-sm px-3 py-2 rounded-md font-semibold text-white bg-blue-500 hover:bg-blue-600">Import Graph(s)</a>
                            </div>
                        </div>


                        <template x-if="remote_data === null">
                            <span class="inline-block my-8 py-1 px-3 bg-gray-200 text-gray-900 rounded">Loading...</span>
                        </template>
                        <template x-if="remote_data !== null">
                            <div class="overflow-y-auto border border-gray-200" style="max-height: 450px;">
                                <table class="style-main-table">
                                    <thead>
                                        <tr>
                                            <th>Id</th>
                                            <th>Name</th>
                                            <th>Total Nodes</th>
                                            {{-- <th>End nodes</th> --}}
                                            <th>Top-level subgraphs</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-if="!(remote_data.graphs ?? false) || remote_data.graphs.length === 0">
                                            <tr colspan="5">
                                                <td>You currently have no graphs.</td>
                                            </tr>
                                        </template>
                                        <template x-for="graph in remote_data.graphs ?? []">
                                            <tr>
                                                <td><span x-text="graph.id"></span></td>
                                                <td><a class="hover:underline text-blue-900" :href="route('dashboard.graph.edit', graph.id)" x-text="graph.name"></a></td>
                                                <td class="text-center text-sm" x-text="graph.amount_nodes"></td>
                                                <td class="text-center text-sm"><span x-text="graph.subgraphs.length" class="rounded px-2 py-1" :class="{ 'bg-gray-300 text-gray-700 cursor-default': graph.subgraphs.length === 0, 'bg-blue-200 text-blue-800 cursor-pointer': graph.subgraphs.length > 0 }"></span></td>
                                                <td class="text-right">
                                                    <a :href="route('graph.show', graph.id)" target="_blank" class="ml-2 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 text-xs px-2 py-1 rounded-md text-white bg-gray-500 hover:bg-gray-600">Use</a>
                                                    <a href="javascript:void(0)" x-on:click="modal_export.open(graph)" class="ml-2 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 text-xs px-2 py-1 rounded-md text-white bg-gray-500 hover:bg-gray-600">Export</a>
                                                    <a href="javascript:void(0)" x-on:click="delete_graph(graph)" class="ml-2 focus:ring-2 focus:ring-offset-2 focus:ring-red-400 text-xs px-2 py-1 rounded-md text-white bg-red-500 hover:bg-red-600">Delete</a>
                                                </td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </template>

                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('body_tail')

    <script>
        function data_index() {
            return {

                init() {

                    this.modal_import.root = this;
                    this.modal_export.root = this;
                    // this.table.parent = this;

                    this.reload_remote();
                },

                route(...args) {
                    // alert('route called with ' + args[0])
                    return route(...args)
                },

                reload_remote() {
                    this.remote_data = null;
                    axios.get(route('dashboard.graph.index.json')).then((resp) => {
                        this.remote_data = resp.data;
                    });
                },

                remote_data: null,

                delete_graph(graph) {
                    // alert('not implemented yet')
                    if (confirm('Are you sure you want to remove the graph: ' + graph.name))
                    {
                        axios.delete(route('dashboard.graph.delete', [graph.id])).then((resp) => {
                            if (resp.data.message && resp.data.message.text)
                            {
                                alert(resp.data.message.text);
                                this.reload_remote()
                            }
                        })
                    }
                },

                modal_import: {
                    root: null,
                    is_open: false,

                    close: function() {
                        this.is_open = false;
                    },
                    open: function() {
                        this.is_open = true;
                    },

                    content: "",

                    message: {
                        content: "",
                        color: "gray",
                        style() {
                            return [`text-${this.color}-700`]
                        },
                    },

                    is_importing: false,

                    file_changed: function(event) {
                        var reader = new FileReader();
                        reader.onload = (event) => {
                            // console.log(event.target.result);
                            this.content = event.target.result;
                        };
                        reader.readAsText(event.target.files[0]);
                    },
                    import: function() {

                        this.is_importing = true;

                        try {
                            let content_parsed = JSON.parse(this.content);
                            // console.log(parsed);
                            axios.post(route('dashboard.graph.import'), { import_data: content_parsed })
                                .then((resp) => {
                                    // console.log(resp);

                                    this.message.content = resp.message ?? 'Import successful'
                                    this.message.color = 'blue'

                                    // if (resp.graph_id)
                                    // {
                                    //     window.location.href = route('dashboard.graph.edit', [resp.graph_id]);
                                    // }

                                    this.root.reload_remote()

                                    this.is_importing = false;
                                })
                                .catch((error) => {

                                    this.message.content = error.response?.message ?? 'An error occured at the server side.'
                                    this.message.color = 'red'

                                    console.error(error.response);

                                    this.is_importing = false;
                                });
                        } catch (e) {
                            this.message.content = 'Input is not valid JSON'
                            this.message.color = 'red'
                            this.is_importing = false;
                        }

                    },
                },

                modal_export: {
                    root: null,

                    is_open: false,
                    message: {
                        content: "",
                        color: "gray",
                        style() {
                            return [`text-${this.color}-700`]
                        },
                    },

                    include_subgraphs: true,

                    graph: {},

                    close: function() {
                        this.is_open = false;
                    },

                    open: function(graph) {
                        this.graph = graph;

                        this.include_subgraphs = true;

                        this.is_open = true;
                    },

                    export() {
                        this.is_exporting = true;

                        // https://stackoverflow.com/a/53775165
                        axios.get(route('dashboard.graph.export', {graph: this.graph.id, include_subgraphs: this.include_subgraphs}), { responseType: 'blob' })
                            .then((resp) => {
                                // console.log(resp);

                                const blob = new Blob([resp.data], { type: 'text/json' })
                                const link = document.createElement('a')
                                link.href = URL.createObjectURL(blob)
                                link.setAttribute('download', resp.headers["content-disposition"].match(/filename="(.*?)"/)[1] ??
                                    'export-graph.json');
                                link.click()
                                URL.revokeObjectURL(link.href)

                                this.is_exporting = false;
                            })
                            .catch((error) => {

                                this.message.content = error.response?.message ?? 'An error occured.'
                                this.message.color = 'red'

                                console.error(error);

                                this.is_exporting = false;
                            });
                    },

                },

            }
        }
    </script>

@endsection
