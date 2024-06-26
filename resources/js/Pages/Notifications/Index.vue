<template>
  <div>
    <div class="flex justify-between items-center">
      <!-- Search -->
    </div>

    <div>
      <!-- Notification tabular data-->
    </div>
    <div class="mt-4">
      <!-- v-else -->
      <p class="mb-4 text-lg font-bold">No Notification to display.</p>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import Layout from '../Shared/Layout'
  import SearchFilter from '../Shared/SearchFilter'
  import Pagination from '../Shared/Pagination'
  import StaffFilter from '../Shared/StaffFilter'
  import Icon from '../Shared/Icon'

  export default {
    metaInfo: { title: 'Notification' },
    layout: (h, page) => h(Layout, [page]),
    props: {},
    data() {
      return {
        form: {},
      }
    },
    components: {
      Pagination,
      SearchFilter,
      StaffFilter,
      Icon,
    },
    watch: {},
    computed: {},
    methods: {},
  }
</script>

<style scoped></style>
