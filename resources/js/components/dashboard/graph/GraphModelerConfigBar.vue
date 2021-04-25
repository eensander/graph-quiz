<template>

    <!-- <span>{{ cell }}</span> -->

    <div v-if="cell == null">
        <span class="p-2">No element is selected</span>
    </div>

    <div v-else-if="cell.isNode()" class="w-full">
        <div class="">
            <span class="m-2 font-bold block text-2xl border-b border-gray-300">Node: {{ cell.store.data.data.node_type }}</span>

            <div v-for="(fields, field_group) in current_fields">
                <div class="p-2">
                    <span class="my-2 block border-b border-gray-300 uppercase font-bold text-sm text-gray-800">{{ field_group }}</span>

                    <div v-for="field in fields" class="block mt-2 mb-1 ">
                        <label v-if="field == 'label'">
                            <span class="text-gray-700 block mb-1">Label</span>
                            <!-- <input class="w-full" type="text" v-model="cell.label" /> -->
                            <div>
                                <textarea style="min-height: 3rem;" class="w-full" type="text" v-model="cell.label" />

                            </div>
                        </label>
                        <label v-else-if="field == 'annotation'">
                            <span class="text-gray-700 block mb-1">Annotation</span>
                            <!-- <input class="w-full" type="text" v-model="cell.store.data.data.options.annotation" /> -->
                            <textarea style="min-height: 3rem;" class="w-full" type="text" v-model="cell.store.data.data.options.annotation" />
                        </label>
                        <label v-else-if="field == 'subgraph'">
                            <span class="text-gray-700 block mb-1">Subgraph</span>
                            <select class="w-full" v-model="cell.store.data.data.options.graph_id">
                                <option :value="undefined">None</option>
                                <option v-for="subgraph_option in subgraph_options" :value="subgraph_option.id">
                                    {{ subgraph_option.id }}) {{ subgraph_option.name }}
                                </option>
                            </select>
                        </label>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <div v-else-if="cell.isEdge()" class="w-full">
        <div class="">
            <span class="m-2 font-bold block text-2xl border-b border-gray-300">Edge</span>

            <div class="p-2">
                <span class="my-2 block border-b border-gray-300 uppercase font-bold text-sm text-gray-800">General</span>

                <div class="block mt-2 mb-1 ">
                    <label>
                        <span class="text-gray-700 block mb-1">Label</span>
                        <input type="text" :value="edge_get_label(cell)" @input="edge_set_label($event)" />
                    </label>
                </div>

            </div>

        </div>
    </div>

    <div v-else>
        <span>Invalid cell selected</span>
    </div>

</template>

<script>

    export default {

        props: {
            cell: {
                type: Object,
                default: null
            },
            default_edge_label: {
                type: Function,
                default: () => {}
            }
        },

        mounted() {
            axios.get(route('dashboard.graph.index.json')).then((resp) => {
                this.subgraph_options = resp.data.graphs;
            });
        },

        data() {
            return {

                // for allowing 'null' option
                null_value: null,

                subgraph_options: null,

                node_type_fields: {
                    start: {
                        general: [
                            'label'
                        ],
                        additional: [
                            'annotation'
                        ],
                    },

                    decision: {
                        general: [
                            'label'
                        ],
                        additional: [
                            'annotation',
                            'subgraph'
                        ],
                    },

                    end: {
                        general: [
                            'label'
                        ],
                        additional: [
                            'annotation'
                        ],
                    },

                    default: {
                        general: [
                            'label'
                        ],
                    },
                },

            }
        },

        computed: {
            current_fields() {
                return this.node_type_fields[this.cell?.store?.data?.data?.node_type ?? 'default']
                    ?? this.node_type_fields['default']
                    ?? {};
            }
        },

        methods: {

            edge_get_label() {
                // return this.cell.store?.data?.labels?.[0] ?? '';
                return this.cell.store?.data?.labels?.[0].attrs.text.text ?? '';
            },

            edge_set_label(e) {

                this.cell.removeLabelAt(0)

                let edge_label = this.default_edge_label(e.target.value)

                this.cell.setLabelAt(0, edge_label);

            },

        },

    }
</script>
