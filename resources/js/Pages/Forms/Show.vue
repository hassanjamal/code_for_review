<template>
  <div>
    <a class="btn btn-black mb-4" :href="route('clients.show', client.id)"
      >Back To Profile</a
    >
    <vue-form-generator ref="vfg" :schema="form" :model="form_model" />
    <img :src="signature" alt="" />
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import VueFormGenerator from 'vue-form-generator'
  import 'vue-form-generator/dist/vfg.css'

  export default {
    metaInfo: { title: 'Forms' },
    layout: (h, page) => h(Layout, [page]),
    props: ['form', 'form_model', 'signature', 'form_styles', 'client'],
    components: {
      'vue-form-generator': VueFormGenerator.component,
    },
  }
</script>

<style>
  textarea,
  input[type] {
    pointer-events: none;
    background-color: #ccc !important;
  }
</style>
