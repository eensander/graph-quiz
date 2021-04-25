require('./bootstrap');

require('@ryangjchandler/spruce');
// console.log(Spruce);

require('alpinejs');

// https://github.com/Maronato/vue-toastification#plugin-registration
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";

import { createApp } from 'vue';

import GraphEditComponent from './components/dashboard/graph/GraphEditorComponent.vue'

const app = createApp({})
    .component('graph-editor-component', GraphEditComponent)
    .use(Toast, {})
    // .component('modal', Modal)

app.mount("#app")
