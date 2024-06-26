// Import Vue from 'vue'
import Vue from 'vue/dist/vue.js'
import VTooltip from 'v-tooltip'
import VueMeta from 'vue-meta'
import PortalVue from 'portal-vue'
import { InertiaApp } from '@inertiajs/inertia-vue'
import VModal from 'vue-js-modal'
import VueSignature from 'vue-signature-pad'
import VueFormGenerator from 'vue-form-generator'
import Vuelidate from 'vuelidate'
import VueMask from 'v-mask'

// Import Froala Editor
/* All Froala styles (also for buttons) */
import fieldSignaturePad from '@/Pages/Shared/fieldSignaturePad.vue'
import 'froala-editor/css/froala_editor.pkgd.min.css'
import 'froala-editor/js/plugins.pkgd.min' // all plugins (you can add plugins by one too)
import VueFroala from 'vue-froala-wysiwyg' // editor
window.Vapor = require('laravel-vapor')

Vue.component('fieldSignaturePad', fieldSignaturePad)
Vue.config.productionTip = false

Vue.mixin({ methods: { route: window.route } })
Vue.use(VTooltip)
Vue.use(InertiaApp)
Vue.use(PortalVue)
Vue.use(VueMeta)
Vue.use(VModal, { dynamic: true, injectModalsContainer: true })
Vue.use(VueSignature)
Vue.use(Vuelidate)
Vue.use(VueMask)
Vue.use(VueFroala)
Vue.use(VueFormGenerator)

let app = document.getElementById('app')

new Vue({
  metaInfo: {
    title: 'Loading…',
    titleTemplate: '%s | QuickerNotes',
  },
  render: (h) =>
    h(InertiaApp, {
      props: {
        initialPage: JSON.parse(app.dataset.page),
        resolveComponent: (name) =>
          import(`@/Pages/${name}`).then((module) => module.default),
      },
    }),
}).$mount(app)
