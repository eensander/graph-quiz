<template>

	<div v-if="loading" class="p-5 text-center">
        <div class="inline-flex items-center text-lg">
            <svg class="inline-block animate-spin -ml-1 mr-3 h-5 w-5 text-gray-900" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            <span>
                Loading {{ subgraph_parent_node != null ? 'subgraph' : 'graph' }}...
            </span>
        </div>
    </div>

	<div v-else-if="graph === null" class="p-5">
		This graph could not be found. This could be due to dependance of a no-longer existing graph.
	</div>

    <div v-else class="space-y-4 ">

        <div class="p-2 bg-gray-100 border-gray-300 border mb-4" v-if="subgraph_parent_node == null">
            You are now using the decision supporting directed graph: {{ graph.name }}
        </div>
        <div class="p-2 bg-gray-100 border-gray-300 border mb-4" v-else>
            You are now using the decision supporting directed <strong>subgraph</strong>: <a target="_blank" class="w-full text-blue-900 hover:underline hover:text-blue-800" v-if="subgraph_parent_node !== null" :href="graph_href()">{{ graph.name }}</a>
        </div>

        <div class="min-h-8 border border-b-4 border-gray-300 relative rounded-sm" :class="classes_node" v-for="(node, node_index) in node_stack">


            <div class="block flex justify-between items-start">

                <span class="py-1 px-2 inline-block " :class="classes_node_type(node)">{{node_number_prefix.concat((node_index + 1), '.') }} {{ node.type }}</span>

                <div class="flex ">

                    <span
                        v-if="node.options.graph_id"
                        class="mr-4 border-l border-b border-r border-gray-300 py-1 px-3 inline-block bg-gray-200 hover:ring-4 hover:ring-pink-300 cursor-pointer"
                        @click="toggle_subgraph_active(node)"
                        :class="{ 'ring-2 ring-offset-4 ring-opacity-50 ring-pink-500': subgraph_active(node) }"><i class="text-lg las la-expand-arrows-alt"></i> {{ subgraph_active(node) ? 'Disable' : 'Enable' }} subgraph</span>

                    <span
                        v-if="node.options.annotation"
                        class="mr-4 border-l border-b border-r border-gray-300 py-1 px-3 inline-block bg-gray-200 hover:bg-gray-300 cursor-pointer"
                        @click="click_node_annotation(node)"
                        :class="{ 'ring-2 ring-offset-4 ring-gray-500': node.dynamic.show_annotation == null || node.dynamic.show_annotation == true }"><i class="text-lg las la-info-circle"></i> Notes</span>

                </div>
            </div>

            <transition name="fade">
                <div class="node-annotation whitespace-pre-wrap  border border-l-8 border-purple-300 p-2 bg-purple-100 md:absolute md:top-0 md:w-60 md:-right-60 md:-mr-4"  v-if="node.options.annotation" v-show="(node.dynamic.show_annotation == null || node.dynamic.show_annotation == true)">
                    {{ node.options.annotation }}
                </div>
            </transition>

            <span v-text="node.content" class="my-1 w-full block text-center text-lg"></span>

            <div v-if="node.options.graph_id && subgraph_active(node)" class="flex justify-center">
                <span
                    class="ring-2 ring-offset-2 ring-opacity-50 ring-gray-300 rounded-sm border border border-gray-300 px-5 my-2 inline-block bg-gray-200 hover:bg-gray-300 cursor-pointer"
                    @click="toggle_subgraph_show(node)"
                    :class="{ 'ring-pink-500': !subgraph_show(node) }"><i class="text-lg las la-expand-arrows-alt"></i> {{ subgraph_show(node) ? 'Hide subgraph' : 'Show subgraph' }} </span>
            </div>

            <!-- <div v-if="node.dynamic.subgraph_active != null && node.dynamic.subgraph_active != false" class="flex items-stretch bg-white border border-pink-300"> -->
            <div v-if="subgraph_active(node)" v-show="subgraph_show(node)" class="flex items-stretch bg-white border-t border-b border-gray-200">

                <div
                    @click="toggle_subgraph_show(node)"
                    class="cursor-pointer w-6 bg-pink-200 border border-pink-500 flex items-center justify-center text-pink-800">
                    {{ subgraph_level+1 }}
                </div>

                <div class="ddbg-pink-50 flex-grow border-l-0 border-t border-b-8 border-pink-400  py-4 pl-4">
                    <graph-component :subgraph_level="subgraph_level+1" :subgraph_parent_node="node" :node_number_prefix="node_number_prefix.concat((node_index + 1), '.')" :graph_id="node.options.graph_id"></graph-component>
                </div>

            </div>


            <div class="edges flex justify-center pt-4 pb-2 px-1">

                <template v-for="edge_out in Object.values(node.edges_out)">
                    <div
                        @click="click_node_edge(node, edge_out)"
                        class="mx-1 px-2 py-1 inline-block "
                        :class="classes_edge(node, edge_out)">

                        <span class="select-none" v-text="(edge_out.content == null ? 'Continue' : edge_out.content)"></span>

                    </div>
                </template>

            </div>

        </div>
    </div>


</template>

<script>

    import { reactive, ref, toRefs, watch } from 'vue'

    import { useToast } from "vue-toastification";
    const toast = useToast();

    export default {

        props: {
            graph_id: {
                type: Number,
                required: true
            },
            node_number_prefix: {
                type: String,
                default: ""
            },
            subgraph_parent_node: {
                type: Object,
                default: null
            },
            subgraph_level: {
                type: Number,
                default: 0
            },

        },

        mounted() {

            this.$watch(
                () => this.node_stack,
                (value, old_value) => { this.node_stack_updated(value, old_value) },
                { deep: true }
            )

            axios.get(route('graph.get', [this.graph_id])).then((resp) => {

                this.graph = resp.data.graph;

                // https://stackoverflow.com/a/14810722
                for (var key in resp.data.nodes) {
                    if (resp.data.nodes.hasOwnProperty(key)) {
                        resp.data.nodes[key].dynamic = [];
                    }
                }
                // return false;

                this.node_cache = resp.data.nodes;

                let start_node = Object.values(this.node_cache).find(node => node.type == 'start');
                if (start_node == null)
                {
                    toast.error("Configuration error: this graph contains no start-node.")
                    console.log('graph contains no start-node')
                    return false;
                }

                this.node_stack.push(start_node);

                this.loading = false;

            }).catch((error) => {
				this.graph = null;
			});
        },

        data() {
            return {

                loading: true,

                graph: {},

                node_cache: {},
                node_stack: [],

            }
        },

        computed: {
            classes_node() {

                let classes = []

                if (this.subgraph_parent_node != null) {
                    classes.push('border-r-0 bg-gray-100')
                } else {
                    classes.push('bg-gray-100')
                }
                return classes
            }
        },

        methods: {

            subgraph_active(node) {
                // console.log(node, node.dynamic.subgraph_active != null && node.dynamic.subgraph_active != false)
                return node.dynamic.subgraph_active != null && node.dynamic.subgraph_active != false
                // subgraph_active ?? false
            },

            subgraph_show(node) {
                return this.subgraph_active(node) &&
                    node.dynamic.subgraph_show == null || node.dynamic.subgraph_show != false
            },

            // This function keeps track of changes to the node_stack
            // to make sure the order stays correct and that no more nodes
            // then the destination node of the last selected option are present
            // in the node_stack. Also this communicates with the parent
            // graph if one is present (and thus this is a subgraph) when
            // an end node is reached.
            // This code could be optimized since
            node_stack_updated: function(value, old_value) {

                // console.log("node_stack updated: ", value);

                // https://stackoverflow.com/a/2641374
                value.every((node, i) => {

                    // console.log(this.node_number_prefix, i, node);

                    let changed = false;
                    let set_end = false;

                    // 1. check for clicked node
                    // 2. check for dynamically set node by subgraph
                    let active_edge = Object.values(node.edges_out).find((edge) => edge.active == true);

                    /**
                     * if i < end
                     *   if current has not active edge:      (modify data object, to force reupdating?)
                     *      set length at current i + 1, changed = true
                     *   if next_node target != current:        (modify argument, to prevent instant updating?)
                     *      rem self, push new, changed = true;
                     */

                    if (i < value.length - 1)
                    {
                        let next_node = value[i+1];

                        if (active_edge != null)
                        {
                            if (active_edge.node_to_id != next_node.id)
                            {

                                let new_node = this.node_cache[active_edge.node_to_id];

                                if (new_node != null)
                                {
                                    // console.log("SET", i, " TO ", new_node.id)
                                    value[i+1] = new_node;
                                    changed = true;
                                }
                                else
                                {
                                    active_edge.active = false;
                                }

                            }
                        }
                        else
                        {
                            set_end = true;
                        }
                    }

                    if (i == value.length - 1)
                    {
                        if (active_edge != null)
                        {
                            let new_node = this.node_cache[active_edge.node_to_id];

                            if (new_node != null)
                            {
                                // console.log("SET", i, " TO ", new_node.id)
                                value[i+1] = new_node;
                                changed = true;
                            }
                        }

                        if (this.subgraph_parent_node != null)
                        {
                            var found_correct_state = false;

                            Object.values(this.subgraph_parent_node.edges_out).forEach(edge => {
                                // let correct_state = (node.type == 'end' && edge.content == node.content);
                                if (node.type != 'end')
                                {
                                    edge.active = false;
                                }
                                else
                                {
                                    if (edge.content == node.content)
                                    {
                                        found_correct_state = true;
                                        edge.active = true;
                                    }
                                    else
                                    {
                                        edge.active = false;
                                    }
                                }
                            });

                            if (node.type == 'end' && found_correct_state == false)
                            {
                                // console.log(this.subgraph_parent_node.edges_out)
                                toast.error(`Warning: content of end-node '${node.content}' in the subgraph does not correspond to any edge in the parent graph.`)
                            }
                        }

                    }

                    if (set_end) {
                        // console.log("END SET")
                        if (value.length > i + 1)
                        {
                            value.length = i + 1;
                            changed = true; // useless if only return
                        }
                    }

                    if (changed) {
                        // console.log("CHANGED")
                        return false;
                    }
                    else
                    {
                        return true;
                    }

                })

            },

            click_node_annotation: function(node) {
                node.dynamic.show_annotation = !(node.dynamic.show_annotation ?? true);
            },

            click_node_edge: function(clicked_node, edge) {

                if (edge.active)
                    return;

                if (this.subgraph_active(clicked_node))
                    return;

                let node_stack_index = this.node_stack.findIndex(x => x == clicked_node);
                // this.node_stack.length = node_stack_index + 1; // remove all items after this index, could also splice?

                Object.values(clicked_node.edges_out).forEach(x => x.active = x == edge);

                // console.log(this.node_cache);

                if (!this.node_cache.hasOwnProperty(edge.node_to_id))
                {
                    return false;
                }

                let node = this.node_cache[edge.node_to_id];
                // let node = Object.values(this.node_cache).find(x => x.id == edge.node_to_id);
                // let node = this.node_cache.filter(x => x.id == edge.node_to_id)[0];

                Object.values(node.edges_out).forEach(x => x.active = false);

                // TODO: notify node occuring > 1 time. could be done in node_stack_updated perhaps

                this.node_stack.push(node);

            },

            toggle_subgraph_active: function(node) {
                node.dynamic.subgraph_active = !(this.subgraph_active(node));
                // node.dynamic.subgraph_show = node.dynamic.subgraph_active;
            },

            toggle_subgraph_show: function(node) {
                node.dynamic.subgraph_show = !(this.subgraph_show(node));
            },

            classes_node_type: function(node) {
                let min = ['border-r border-b  rounded-tl-sm'];
                let type_map = {
                    'start': [
                        'bg-yellow-100',
                        'text-yellow-900',
                        'border-yellow-200',
                    ],
                    'decision': [
                        'bg-blue-100',
                        'text-blue-900',
                        'border-blue-200',
                    ],
                    'end': [
                        'bg-red-100',
                        'text-red-900',
                        'border-red-200',
                    ],
                    'notice': [
                        'bg-green-200',
                        'text-green-900',
                        'border-green-300',
                    ],
                };

                return min.concat(type_map[node.type] ?? []);

            },

            classes_edge: function(node, edge) {

                let classes = [
                    'border-b-4 rounded-sm'
                ];

                if (this.subgraph_active(node))
                {
                    classes.push('cursor-not-allowed');

                    if (edge.active ?? false)
                    {
                        classes.push('bg-red-500 text-white');
                        classes.push('border-red-800 ');
                    }
                    else
                    {
                        classes.push('bg-gray-500 text-white');
                        classes.push('border-gray-700 ');
                    }

                }
                else
                {
                    classes.push('cursor-pointer');

                    if (edge.active ?? false)
                    {
                        classes.push('bg-green-500 hover:bg-green-400 text-white');
                        classes.push('border-green-600');
                    }
                    else
                    {

                        classes.push('bg-gray-600 hover:bg-gray-500 text-white');
                        classes.push('border-gray-800');
                    }
                }

                return classes;

            },

            graph_href() {
                return route('graph.show', [this.graph_id]);
            }

        },

    }
</script>

<style lang="scss">
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

</style>
