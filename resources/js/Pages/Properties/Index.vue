<template>
  <div>
    <div class="mb-8 flex justify-between items-center">
      <h1 class="font-bold text-3xl">Properties</h1>
      <inertia-link class="btn btn-black" :href="route('properties.create')">
        <span>Add</span>
        <span class="hidden md:inline">Property</span>
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Provider</th>
          <th class="px-6 pt-6 pb-4">API ID</th>
          <th class="px-6 pt-6 pb-4" colspan="2">Verification</th>
        </tr>
        <property-table-row
          v-for="property in properties"
          :key="property.id"
          :property="property"
        />
        <tr v-if="properties.length === 0">
          <td class="border-t px-6 py-4" colspan="4">No properties found.</td>
        </tr>
      </table>
    </div>
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import PropertyTableRow from './PropertyTableRow'

  export default {
    metaInfo: { title: 'Properties' },
    layout: (h, page) => h(Layout, [page]),
    props: ['properties'],
    components: {
      PropertyTableRow,
    },
    data() {
      return {
        sending: false,
        form: {
          search: null,
          trashed: false,
        },
      }
    },

    methods: {
      reset() {
        this.form.search = null
      },
    },
  }
</script>
