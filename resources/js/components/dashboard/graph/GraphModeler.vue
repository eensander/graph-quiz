<template>

    <div class="border border-gray-400 rounded overflow-hidden">
        <div class="w-full bg-gray-100 ">
            <GraphModelerToolbar v-if="graph !== null" :remote_graph_data="remote_graph_data" :graph="graph" :remote_graph_data_reload="remote_graph_data_reload" :local_save="local_save"></GraphModelerToolbar>
        </div>
        <div class="w-full flex" style="height: 500px;">
            <div class="w-20 bg-gray-100 border-t  border-gray-200">
                <GraphModelerElementsBar v-if="graph !== null" :graph="graph" :node_type_defaults="node_type_defaults"></GraphModelerElementsBar>
            </div>
            <div class="w-32 flex-grow border border-b-0 border-gray-200">
                <div class="relative h-full" id="modeler-container"></div>
            </div>
            <div class="w-64 bg-gray-100 border-t border-gray-200 overflow-y-auto">
                <GraphModelerConfigBar :default_edge_label="default_edge_label" :cell="selected_cell"></GraphModelerConfigBar>
            </div>
        </div>
    </div>

</template>

<script>

    import { useToast } from "vue-toastification";
    const toast = useToast();

    import { Graph } from '@antv/x6'

    import GraphModelerConfigBar from './GraphModelerConfigBar.vue'
    import GraphModelerElementsBar from './GraphModelerElementsBar.vue'
    import GraphModelerToolbar from './GraphModelerToolbar.vue'

    const default_node_ports = {
        groups: {
            out: {
                attrs: {
                    circle: {
                        r: 8,
                        magnet: true,
                    }
                },
                position: 'bottom'
            },
        },
        items: [
            {
                id: 'out-1',
                group: 'out',
            },
        ]
    };

    export default {

        components: {
            GraphModelerConfigBar,
            GraphModelerElementsBar,
            GraphModelerToolbar,
        },

        props: {
            remote_graph_data: {
                type: Object,
                required: true,
            },
            remote_graph_data_reload: {
                type: Function,
                required: true,
            },
            root_graph_id: {
                type: Number,
                required: true,
            },
        },

        mounted() {
            console.log('Editor window mounted.')

            // console.log(this.remote_graph_data)

            this.init_modeler()

            this.$watch(
                () => this.remote_graph_data,
                (value, old_value) => { console.log("remte chngd", value, old_value); this.remote_load() },
            )

        },

        data() {
            return {

                graph_data: {},
                graph: null,

                selected_cell: null,

                default_edge_label: (text = '') => {
                    return {
                        attrs: {
                            text: {
                                text: text,
                            }
                        },
                        position: {
                            distance: .25,
                        },
                    }
                },

                node_type_defaults: {
                    'start': {
                        shape: 'ellipse',
                        tools: ['button-remove'],
                        width: 100,
                        height: 40,
                        attrs: {
                            body: {
                                fill: '#FDE68A', // reference tailwind-css's default colors
                                stroke: '#78350F',
                                strokeWidth: 1,
                            },
                            label: {
                                fill: '#78350F',
                                fontSize: 13,
                                textWrap: { width: -10 }
                            },
                        },

                        data: {
                            node_type: 'start',
                            options: {}
                        },

                        // ports: [
                        //     { id: 'out', group: 'out', position: 'bottom' },
                        // ],

                        ports: default_node_ports,

                    },
                    /*
                    'decision': {
                        shape: 'html',
                        tools: ['button-remove'],
                        width: 60,
                        height: 60,
                        attrs: {
                            body: {
                                fill: '#93C5FD', // reference tailwind-css's default colors
                                stroke: '#1E3A8A',
                                strokeWidth: 1,
                                style: 'transform-box: fill-box;transform-origin: center;',
                                transform: 'rotate(45)',
                            },
                            label: {
                                fill: '#1E3A8A',
                                fontSize: 13,
                                textWrap: { width: 100 }
                            },
                        },
                        // html: {
                        //     render(node) {
                        //       return(
                        //         `<div>
                        //           <span>${data.label}</span>
                        //         </div>`
                        //       )
                        //     },
                        //     // shouldComponentUpdate(node) {
                        //     //   // 控制节点重新渲染
                        //     //   return node.hasChanged('data')
                        //     // },
                        // // }
                        // },

                        ports: default_node_ports,
                    },
                    */
                    'decision': {
                        shape: 'polygon',
                        tools: ['button-remove'],
                        width: 80,
                        height: 80,
                        // https://x6.antv.vision/zh/examples/node/native-node#polygon
                        points: '0,10 10,0 20,10 10,20',
                        attrs: {
                            body: {
                                fill: '#93C5FD', // reference tailwind-css's default colors
                                stroke: '#1E3A8A',
                                strokeWidth: 1,

                            },
                            label: {
                                fill: '#1E3A8A',
                                fontSize: 13,
                                textWrap: { width: 100 }
                            },
                        },

                        data: {
                            node_type: 'decision',
                            options: {}
                        },

                        ports: default_node_ports,
                    },
                    'notice': {
                        shape: 'rect',
                        tools: ['button-remove'],
                        width: 100,
                        height: 40,
                        attrs: {
                            body: {
                                fill: '#A7F3D0', // reference tailwind-css's default colors
                                stroke: '#064E3B',
                                strokeWidth: 1,
                            },
                            label: {
                                fill: '#064E3B',
                                fontSize: 13,
                                textWrap: { width: -10 }
                            }
                        },

                        data: {
                            node_type: 'notice',
                            options: {}
                        },

                        ports: default_node_ports,
                    },
                    'end': {
                        shape: 'ellipse',
                        tools: ['button-remove'],
                        width: 100,
                        height: 40,
                        attrs: {
                            body: {
                                fill: '#FECACA', // reference tailwind-css's default colors
                                stroke: '#7F1D1D',
                                strokeWidth: 1,
                            },
                            label: {
                                fill: '#7F1D1D',
                                fontSize: 13,
                                textWrap: { width: -10 }
                            },
                        },

                        data: {
                            node_type: 'end',
                            options: {}
                        },

                        ports: default_node_ports,
                    },
                    'default': {
                        shape: 'ellipse',
                        tools: ['button-remove'],
                        width: 100,
                        height: 40,
                        attrs: {
                            body: {
                                fill: '#6B7280', // reference tailwind-css's default colors
                                stroke: '#991B1B',
                            },
                            label: {
                                fill: '#991B1B',
                                fontSize: 13,
                                textWrap: { width: -10 }
                            },
                        },

                        data: {
                            node_type: 'default',
                            options: {}
                        },

                        ports: default_node_ports,
                    },
                }

            }
        },

        methods: {
            init_modeler() {

                let container = document.getElementById('modeler-container');
                let width = container.scrollWidth;
                let height = container.scrollHeight || 500;

                this.graph = new Graph({
                    container: container,
                    grid: true,
                    width,
                    height,
                    mousewheel: {
                        enabled: true,
                        modifiers: ['ctrl', 'meta'],
                    },
                    scroller: {
                        enabled: true,
                        pannable: true,
                    },
                    history: {
                        enabled: true,
                        beforeAddCommand(event, args) {
                            // console.log(event, args);
                            // prevent adding/removing tools on hover to be added to history
                            if (args.key == 'tools')
                            {
                                return false
                            }
                        },
                    },
                    resizing: {
                        enabled: true,
                    },
                    selecting: {
                        enabled: true,
                        // showNodeSelectionBox: true,
                    },

                    connecting: {
                        // anchor: 'center',
                        // connectionPoint: 'anchor',

                        // https://x6.antv.vision/en/docs/tutorial/intermediate/interacting/#%E8%BF%9E%E7%BA%BF%E8%A7%84%E5%88%99
                        allowBlank: false,
                        allowMulti: false,
                        allowLoop: false,
                        allowNode: true,
                        allowEdge: false,
                        allowPort: false,

                        highlight: true,
                        snap: true,

                        validateEdge(e) {
                            const edge = e.edge;
                            if (edge != null)
                            {
                                const source_node_id = e.edge.store?.data?.source?.cell ?? null;
                                if (source_node_id != null)
                                    e.edge.setSource({cell: source_node_id}) // move from output port to node itself, for 'easier' saving

                                e.edge.addTools();
                            }

                            return true;
                        },
                    },

                })

                this.remote_load()

                // events
                // https://x6.antv.vision/en/docs/tutorial/intermediate/events

                // this.graph.on('cell:click', ({ e, x, y, cell, view }) => {
                this.graph.on('cell:selected', ({ e, x, y, cell, view }) => {
                    // console.log(cell)
                    if (this.selected_cell != cell)
                    {
                        this.selected_cell = cell
                    }
                })

                this.graph.on('cell:unselected', ({ e, x, y, cell, view }) => {
                    if (this.selected_cell != null)
                        this.selected_cell = null
                })

                this.graph.on('blank:click', ({ e, x, y }) => {
                    if (this.selected_cell != null)
                        this.selected_cell = null
                })

                // /*
                // https://x6.antv.vision/en/docs/api/registry/edge-tool
                this.graph.on('edge:mouseenter', ({ cell }) => {
                    // console.log(cell)
                    cell.addTools(
                        [
                            {
                                name: 'button-remove',
                                args: {
                                    distance: 20,
                                    fill: '#00ff00'
                                },
                            },
                            'segments'
                        ],
                        'onhover',
                    )
                })

                this.graph.on('edge:mouseleave', ({ cell }) => {
                    cell.removeTools('onhover')
                    // cell.removeTools()
                })
                // */

                window.graph_main = this.graph;

                // console.log();

            },

            local_save() {

                let serialized = this.local_serialize();

                // TODO: register route and parse updates in controller
                axios.put(route('dashboard.graph.update', [this.root_graph_id]), serialized).then((resp) => {
                    if(resp.data.messages)
                    {
                        for (let message of resp.data.messages)
                        {
                            toast(message.text, { type: message.type })
                        }
                    }
                    if (resp.data.message && resp.data.message.text && resp.data.message.type)
                    {
                        toast(resp.data.message.text, { type: resp.data.message.type })
                    }
                }).catch((error) => {
                    if (error.response.data.message && error.response.data.errors)
					{
                        console.log(error.response)
						toast(error.response.data.message, { type: 'error' });
						for (let err of Object.values(error.response.data.errors).flat()){
							toast(err, { type: 'error' })
						}
					}
				});
            },

            local_serialize() {
                // const local_data = this.graph.toJSON();
                const local_data = {
                    'nodes': this.graph.getNodes(),
                    'edges': this.graph.getEdges(),
                };

                console.log("local:", local_data);

                let serialized = {
                    nodes: [],
                    edges: [],
                }

                for(let loc_node of local_data['nodes']) {
                    // console.log(loc_node);

                    let rem_node = {
                        // 'id': loc_node.store.data?.data?.node_id ?? 0,
                        'id': loc_node.id,
                        'content': loc_node.label ?? '',
                        'node_type': loc_node.store.data?.data?.node_type ?? 'default',
                        'options': {
                            'appearance': {
                                // 'width': loc_node.store.data?.size?.width ?? null,
                                // 'height': loc_node.store.data?.size?.height ?? null,
                                'x': loc_node.store.data?.position?.x ?? null,
                                'y': loc_node.store.data?.position?.y ?? null,
                                'width': loc_node.store.data?.size?.width ?? null,
                                'height': loc_node.store.data?.size?.height ?? null,
                            },
                            // TODO: from all data.fields, to raw. TODO in deserialize: opposite
                            // ALTERNATIVELY (chosen): in deserialize don't load appearance?
                            // still filter on appearance to prevent collision
                            // ...Object.fromEntries(
                            //     Object.entries(loc_node.store?.data).filter(([key, value]) => key === 'appearance') )
                            // ...loc_node.store?.data?.data?.options ?? {}
                            // other method, same as in deserialization
                            ...Object.fromEntries(Object.entries(loc_node.store?.data?.data?.options ?? {}).filter(([key, value]) => {
                                return !['node_id', 'appearance'].includes(key);
                            }))
                        },
                    }

                    // console.log(rem_node);

                    serialized['nodes'].push(rem_node)
                }

                for(let loc_edge of local_data['edges']) {

                    // console.log(loc_edge);

                    let rem_edge = {
                        'id': loc_edge.store.data?.data?.edge_id ?? 0,
                        // 'node_from_id': parseInt(loc_edge.store.data?.source?.cell.split('-')[1],10) ?? null,
                          // 'node_to_id': parseInt(loc_edge.store.data?.target?.cell.split('-')[1],10) ?? null,
                        'node_from_id': loc_edge.store.data?.source?.cell,
                          'node_to_id': loc_edge.store.data?.target?.cell,
                        'content': loc_edge.labels?.[0]?.attrs?.text?.text ?? null,
                        'options': {
                            'appearance': {
                                'vertices': loc_edge.store.data?.vertices,
                            },
                        },
                    }

                    // console.log(rem_edge);

                    serialized['edges'].push(rem_edge)
                }


                console.log("serialized:", serialized);

                return serialized;

            },

			remote_load() {
                this.graph.fromJSON(this.remote_deserialize());
            },

            remote_deserialize() {

                console.log('remote: ', this.remote_graph_data);

                // https://x6.antv.vision/en/docs/tutorial/intermediate/serialization
                // https://github.com/antvis/X6/blob/master/sites/x6-sites-demos/packages/tutorial/intermediate/tools/basic/src/app.tsx

                let data_nodes = [];
                let x = 0; let y = 0;
                Object.values(this.remote_graph_data.nodes).forEach((node) => {

                    let node_ser = {
                        x: node.options?.appearance?.x ?? 50,
                        y: node.options?.appearance?.y ?? 50,

                        // width: node.options?.appearance?.width ?? 100,
                        // height: node.options?.appearance?.height ?? 100,

                        // width: node.options?.appearance?.width ?? null,
                        // height: node.options?.appearance?.height ?? null,

                        id: `node-${node.id}`,
                        label: node.content,
                        data: {
                            node_id: node.id,
                            node_type: node.type,
                            // https://stackoverflow.com/a/62400741 , see referemce in serialize fn.
                            options: Object.fromEntries(Object.entries(node.options).filter(([key, value]) => {
                                return !['node_id', 'node_type', 'appearance'].includes(key);
                            }))
                        },
                    }

                    // https://stackoverflow.com/a/58245240
                    node_ser = Object.assign(
                        {},
                        this.node_type_defaults[node.type] ?? this.node_type_defaults['default'] ?? {},
                        node_ser,
                    );

                    // because there are defaults
                    if (node.options?.appearance?.width != null)
                    {
                        node_ser.width = node.options.appearance.width;
                    }

                    if (node.options?.appearance?.height != null)
                    {
                        node_ser.height = node.options.appearance.height;
                    }

                    data_nodes.push(node_ser);
                })


                let data_edges = [];
                Object.values(this.remote_graph_data.edges).forEach((edge) => {
                    let edge_ser = {
                        id: `edge-${edge.id}`,

                        labels: [
                            this.default_edge_label(edge.content)
                            // {attrs: {
                            //     text: {
                            //         text: "a"
                            //     }
                            // }}
                        ],

                        shape: 'edge',
                        // https://x6.antv.vision/zh/docs/api/registry/router#orth
                        // router: {
                        //     name: 'orth',
                        // },
                        connector: {
                            name: 'jumpover',
                            args: {
                                type: 'arc',
                            },
                        },
                        // attrs: {
                        //
                        // },
                        source: `node-${edge.node_from_id}`,
                        target: `node-${edge.node_to_id}`,
                        data: {
                            edge_id: edge.id,
                        },
                    }

                    // let edge_label = default_edge_label();

                    // console.log(edge.content)
                    // edge_label.attrs.text.text = edge.content;
                    //
                    // console.log(edge_label)

                    // edge_ser.labels = [edge_label];

                    data_edges.push(edge_ser)
                })


                let data = {
                    nodes: data_nodes,
                    edges: data_edges,
                }

                // this.graph.fromJSON(data);

                return data;


            },

        },

    }
</script>
