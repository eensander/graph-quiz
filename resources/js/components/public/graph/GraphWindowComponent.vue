<template>

    <div class="my-16 mx-2 md:mx-auto max-w-6xl">
        <div>
            <graph-component :graph_id="root_graph_id"></graph-component>
        </div>
    </div>

</template>

<script>

    import GraphComponent from './GraphComponent'

    export default {

        components: {
            'graph-component': GraphComponent
        },

        mounted() {
            // ...
        },

        data() {
            return {

                root_graph_id: parseInt(route().params.graph, 10),

                graph: {},

                node_cache: {},
                node_stack: [],

            }
        },

        methods: {
            // ...
        },

    }
</script>
