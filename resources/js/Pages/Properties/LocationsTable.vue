<template>
  <div>
    <h2 class="mt-12 font-bold text-2xl">Locations</h2>
    <div class="mt-6 bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">City</th>
          <th class="px-6 pt-6 pb-4">Phone</th>
          <th class="px-6 pt-6 pb-4">Status</th>
          <th class="px-6 pt-6 pb-4">Subscription</th>
        </tr>
        <locations-table-row
          v-if="locations.length"
          v-for="location in locations"
          :key="location.id"
          :location="location"
          :subscription="location.subscription"
        />
        <tr v-if="!locations.length">
          <td class="border-t px-6 py-4" colspan="5">No locations found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
  import LocationsTableRow from './LocationsTableRow'

  export default {
    props: ['locations'],
    components: {
      LocationsTableRow,
    },
  }
</script>

<style scoped></style>
