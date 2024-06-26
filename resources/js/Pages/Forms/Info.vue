<template>
  <div>
    <h1>{{ info_type }}</h1>
    <p>{{ info }}</p>
  </div>
</template>

<script>
  export default {
    metaInfo: { title: 'Forms Status' },
    props: ['info', 'info_type'],
    components: {},
    data() {
      return {}
    },
    methods: {},
  }
</script>

<style></style>
