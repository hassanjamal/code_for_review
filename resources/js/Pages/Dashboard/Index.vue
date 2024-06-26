<template>
  <layout title="Dashboard">
    <h3 class="text-2xl mb-4">Welcome to your dashboard.</h3>
    <ul class="list-inside list-disc">
      <li class="mb-2">
        For staff members, the dashboard will display commonly used information
        like stats on visits, notifications and unfinished notes.
      </li>
      <li class="mb-2">
        For account-admins, the dashboard will display information important to
        managing the locations of the business.
      </li>
    </ul>
  </layout>
</template>

<script>
  import Layout from '../Shared/Layout'

  export default {
    name: 'Dashboard',
    components: {
      Layout,
    },
    data() {
      return {}
    },
  }
</script>

<style scoped></style>
