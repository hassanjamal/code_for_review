<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-blue-900 hover:text-blue-800"
        :href="route('templates.index')"
        >Templates
      </inertia-link>
      <span class="text-blue-500 font-medium">/</span> Add New
    </h1>

    <template-editor :form="form" :save="save" />
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import TemplateEditor from './TemplateEditor'
  import TextInput from '../Shared/TextInput'

  export default {
    metaInfo: { title: 'New Template' },
    layout: (h, page) => h(Layout, [page]),
    components: {
      TemplateEditor,
      TextInput,
    },
    data() {
      return {
        sending: false,
        form: {
          name: '',
          default_short_name: '',
          default_group_name: '',
          content: '',
        },
      }
    },
    methods: {
      copyText(event) {
        console.log(event.target.innerText)
      },
      save() {
        this.$inertia.post(route('templates.store', this.form))
      },
    },
  }
</script>

<style scoped>
  .error {
    @apply bg-red-100;
  }
</style>
