<template>
  <layout title="Dashboard">
    This view needs to display login/register information for customers.
    <a class="btn btn-sm btn-black" :href="route('register')">Register</a>
  </layout>
</template>

<script>
  import Layout from './Shared/Layout'

  export default {
    name: 'Home',
    components: { Layout },
  }
</script>

<style scoped></style>
