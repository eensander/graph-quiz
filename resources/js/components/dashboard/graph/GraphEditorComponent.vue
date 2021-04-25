<template>

    <div class="mb-8 mt-4 mx-auto max-w-6xl">
        <!--
        <div class="tab-buttons">
            <span class="tab-item active">Modeler</span>
            <span class="tab-item">Information</span>
        </div> -->

        <div>
            <div>

                <GraphModeler v-if="remote_graph_data != null" :root_graph_id="root_graph_id" :remote_graph_data_reload="remote_graph_data_reload" :remote_graph_data="remote_graph_data"></GraphModeler>
                <div v-else>
                    <span>Loading graph data...</span>
                </div>

            </div>
        </div>

    </div>

</template>

<script>

    // import G6 from '@antv/g6';
    // import x6 from '@antv/x6';
    import { Graph } from '@antv/x6'

    import GraphModeler from './GraphModeler.vue'

    // import Modal from '../shared/Modal.vue'

    export default {

        components: {
            GraphModeler
        },

        mounted() {
            console.log('Editor window mounted.')

            this.remote_graph_data_reload();
        },

        data() {
            return {

                root_graph_id: parseInt(route().params.graph, 10),

                remote_graph_data: null,

                graph: null,

                nodes: {},
                edges: [],

            }
        },

        methods: {
            remote_graph_data_reload() {
                axios.get(route('graph.get', [this.root_graph_id])).then((resp) => {

                    // this.graph_data = resp.data.graph;

                    this.remote_graph_data = resp.data;

                });
            }
        }

    }
</script>
<!--
<style lang="scss">
    .tab-buttons {
        @apply w-full flex mb-6 border-b border-gray-200;

        .tab-item {

            @apply py-2 px-3 text-gray-800 border-b-4 border-transparent cursor-pointer;

            &:hover {
                @apply bg-gray-100
            }

            &.active {
                @apply border-gray-400 bg-gray-100
            }
        }
    }
</style> -->
