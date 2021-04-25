<template>

    <div class="shape-holder">
        <div class="shape" data-type="start" @mousedown.native="startDrag($event)">
            Start
        </div>
        <div class="shape" data-type="decision" @mousedown.native="startDrag($event)">
            Decision
        </div>
        <div class="shape" data-type="notice" @mousedown.native="startDrag($event)">
            Notice
        </div>
        <div class="shape" data-type="end" @mousedown.native="startDrag($event)">
            End
        </div>
    </div>

</template>

<script>
    // https://github.com/antvis/X6/blob/master/sites/x6-sites-demos/packages/tutorial/basic/dnd/dnd/src/app.tsx

    import { Dom, Addon } from '@antv/x6';

    export default {

        props: {
            graph: {
                type: Object,
                required: true,
            },
            node_type_defaults: {
                type: Object,
                required: true,
            }
        },

        mounted() {

            // https://github.com/antvis/X6/blob/master/sites/x6-sites-demos/packages/tutorial/basic/dnd/dnd/src/app.tsx
            // https://x6.antv.vision/en/docs/tutorial/basic/dnd
            this.dnd = new Addon.Dnd({
              target: this.graph,
              scaled: false,
              animation: true,
              validateNode(droppingNode, options) {
                  // alert('validating')
                return true;
                // return droppingNode.shape === 'html'
                //   ? new Promise<boolean>((resolve) => {
                //       const { draggingNode, draggingGraph } = options;
                //       const view = draggingGraph.findView(draggingNode)
                //       const contentElem = view.findOne('foreignObject > body > div');
                //       Dom.addClass(contentElem, 'validating')
                //       setTimeout(() => {
                //         Dom.removeClass(contentElem, 'validating')
                //         resolve(true)
                //       }, 3000)
                //     })
                //   : true
              },
            })

            console.log(this.dnd);

        },

        data() {
            return {
                dnd: null,
            }
        },

        methods: {
            startDrag(e) {

                console.log(e)

                /*
                const types = {
                    'rect': {
                        width: 100,
                        height: 40,
                        attrs: {
                            label: {
                                text: 'Rect',
                                fill: '#6a6c8a',
                            },
                            body: {
                                stroke: '#31d0c6',
                                strokeWidth: 2,
                            },
                        },
                    },
                    'circle': {
                        width: 100,
                        height: 40,
                        shape: 'html',
                        html: () => {
                            const wrap = document.createElement('div')
                            wrap.style.width = '100%'
                            wrap.style.height = '100%'
                            wrap.style.display = 'flex'
                            wrap.style.alignItems = 'center'
                            wrap.style.justifyContent = 'center'
                            wrap.style.border = '2px solid rgb(49, 208, 198)'
                            wrap.style.background = '#fff'
                            wrap.style.borderRadius = '100%'
                            wrap.innerText = 'Circle'
                            return wrap
                        },
                    }
                };
                */

                const target = e.currentTarget
                const type = target.getAttribute('data-type')

                if (!type in this.node_type_defaults) {
                    return false
                }

                const node = this.graph.createNode(this.node_type_defaults[type]);
                node.label = type

                // alert('ay')
                console.log(node);

                this.dnd.start(node, e)

            },
        },

    }
</script>

<style lang="scss">

    .shape-holder {

        user-select: none;
        margin-top: 2rem;

        .shape {
            @apply mb-1 p-2 bg-gray-200 border-gray-300 border-b-2  border-t;
        }

    }

</style>
