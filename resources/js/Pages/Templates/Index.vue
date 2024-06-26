<template>
  <div>
    <div class="flex justify-between items-center">
      <!-- Search -->
    </div>
    <div class="mt-8 mb-8">
      <inertia-link class="btn btn-black" :href="route('templates.create')">
        New Template
      </inertia-link>
    </div>
    <div class="bg-white rounded shadow overflow-x-auto">
      <table v-if="templates.length" class="w-full whitespace-no-wrap">
        <tr class="text-left font-bold">
          <th class="px-6 pt-6 pb-4">Name</th>
          <th class="px-6 pt-6 pb-4">Short Name</th>
          <th class="px-6 pt-6 pb-4">Group</th>
          <th class="px-6 pt-6 pb-4" colspan="2"></th>
        </tr>
        <tr
          v-for="template in templates"
          :key="template.id"
          class="hover:bg-gray-100 focus:bg-gray-100 focus:outline-none"
        >
          <td class="border-t px-6 py-4">{{ template.name }}</td>
          <td class="border-t px-6 py-4 capitalize">
            {{
              template.default_short_name ? template.default_short_name : '-'
            }}
          </td>
          <td class="border-t px-6 py-4 uppercase">
            {{
              template.default_group_name ? template.default_group_name : '-'
            }}
          </td>
          <td class="border-t px-6 py-4 text-gray-600 text-right">
            <inertia-link
              class="underline mr-2 hover:text-gray-900"
              method="DELETE"
              :href="route('templates.destroy', template.id)"
              >delete</inertia-link
            >
          </td>
          <td class="border-t w-px">
            <inertia-link
              class="px-4 flex focus:outline-none"
              :href="route('templates.edit', template.id)"
              tabindex="-1"
            >
              <icon
                name="cheveron-right"
                class="block w-6 h-6 fill-current text-gray-500"
              />
            </inertia-link>
          </td>
        </tr>
      </table>

      <div v-else>
        <p class="text-lg text-gray-600 px-6 py-4">
          This organization has not created any templates.
        </p>
      </div>
    </div>
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'
  import Icon from '../Shared/Icon'

  export default {
    metaInfo: { title: 'Templates' },
    layout: (h, page) => h(Layout, [page]),
    components: {
      Icon,
    },
    props: {
      templates: {
        type: Array,
      },
    },
  }
</script>

<style scoped></style>
