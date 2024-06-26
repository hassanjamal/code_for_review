<template>
  <div class="w-full">
    <h1 class="text-2xl font-bold mb-8">
      <a class="text-blue-600" :href="route('templates.index')">Templates /</a>
      {{ template.id }}
    </h1>
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import Icon from '../Shared/Icon'
  import TextInput from '../Shared/TextInput'

  export default {
    metaInfo: { title: 'Templates' },
    layout: (h, page) => h(Layout, [page]),
    props: {
      template: Object,
    },
    data() {
      return {
        form: {},
      }
    },
    components: {
      Icon,
      TextInput,
    },

    methods: {},
  }
</script>

<style scoped></style>
