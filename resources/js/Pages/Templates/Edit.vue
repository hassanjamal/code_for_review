<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-blue-600 hover:text-blue-800"
        :href="route('templates.index')"
        >Templates
      </inertia-link>
      <span class="text-blue-500 font-medium">/</span> Edit
    </h1>
    <template-editor :form="form" :save="save" :valid-form="validForm" />
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import TextInput from '../Shared/TextInput'
  import TemplateEditor from './TemplateEditor'

  export default {
    metaInfo: { title: 'Edit Template' },
    layout: (h, page) => h(Layout, [page]),
    props: ['template'],
    components: {
      TemplateEditor,
      TextInput,
    },
    data() {
      return {
        form: {
          name: this.template.name,
          default_short_name: this.template.default_short_name,
          default_group_name: this.template.default_group_name,
          content: this.template.content,
        },
      }
    },
    computed: {
      validForm() {
        return this.form.content && this.form.name
      },
    },
    methods: {
      save() {
        this.$inertia.put(
          route('templates.update', this.template.id),
          this.form,
        )
      },
    },
  }
</script>

<style scoped>
  .error {
    @apply bg-red-100;
  }
</style>
