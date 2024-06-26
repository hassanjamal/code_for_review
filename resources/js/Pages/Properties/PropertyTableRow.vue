<template>
  <tr class="hover:bg-gray-100 focus:bg-gray-100 focus:outline-none">
    <td class="border-t px-6 py-4 flex items-center">
      <form @submit.prevent="update" class="inline">
        <label :for="'name_' + property.id" class="sr-only"
          >Property Name</label
        >
        <input
          @blur.prevent="update"
          class="px-2 py-1 rounded border border-gray-200 hover:border-brand-light hover:shadow bg-white"
          type="text"
          :id="'name_' + property.id"
          name="api_identifier"
          v-model="property.name"
        />
      </form>
      <icon
        v-if="property.deleted_at"
        name="trash"
        class="flex-shrink-0 w-3 h-3 fill-gray ml-2"
      />
    </td>
    <td class="border-t uppercase">
      {{ property.api_provider }}
    </td>
    <td class="border-t text-center">
      <form v-if="canUpdate" @submit.prevent="update" class="inline">
        <label :for="'api_identifier_' + property.id" class="sr-only"
          >API Identifier</label
        >
        <input
          @blur.prevent="update"
          class="px-2 py-1 rounded border border-gray-200 hover:border-brand-light hover:shadow bg-white"
          type="text"
          :id="'api_identifier_' + property.id"
          name="api_identifier"
          v-model="property.api_identifier"
        />
      </form>
      <div v-else>
        <p v-text="property.api_identifier"></p>
      </div>
    </td>
    <td class="border-t">
      <div v-if="property.activation_code">
        <div v-if="property.verified" class="text-green-500 flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="icon-bolt h-6 w-6 mr-2"
          >
            <circle
              cx="12"
              cy="12"
              r="10"
              class="fill-current text-green-500"
            />
            <path
              class="fill-current text-white"
              d="M14 10h2a1 1 0 0 1 .81 1.58l-5 7A1 1 0 0 1 10 18v-4H8a1 1 0 0 1-.81-1.58l5-7A1 1 0 0 1 14 6v4z"
            />
          </svg>
          Verified
        </div>
        <div v-else class="flex items-center">
          <svg
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 24 24"
            class="icon-close-circle h-6 w-6 mr-2"
          >
            <circle cx="12" cy="12" r="10" class="fill-current text-red-500" />
            <path
              class="fill-current text-white"
              d="M13.41 12l2.83 2.83a1 1 0 0 1-1.41 1.41L12 13.41l-2.83 2.83a1 1 0 1 1-1.41-1.41L10.59 12 7.76 9.17a1 1 0 0 1 1.41-1.41L12 10.59l2.83-2.83a1 1 0 0 1 1.41 1.41L13.41 12z"
            />
          </svg>
          <button class="btn btn-sm btn-gray" @click="verifyProperty">
            Verify
          </button>
        </div>
      </div>
    </td>
    <td class="border-t w-px">
      <inertia-link
        class="px-4 flex items-center focus:outline-none"
        :href="route('properties.edit', property.id)"
        tabindex="-1"
      >
        <icon
          name="cheveron-right"
          class="block w-6 h-6 fill-current text-gray-500"
        />
      </inertia-link>
    </td>
  </tr>
</template>
<script>
  import Icon from '../Shared/Icon'
  import VerifyPropertyModal from './VerifyPropertyModal'

  export default {
    name: 'PropertyTableRow',
    components: { Icon },
    props: ['property'],
    data() {
      return {
        initialApiIdentifier: this.property.api_identifier,
        initialName: this.property.name,
      }
    },
    computed: {
      canUpdate() {
        return !this.property.verified
      },
      needsUpdate() {
        return (
          this.property.api_identifier !== this.initialApiIdentifier ||
          this.property.name !== this.initialName
        )
      },
    },
    methods: {
      update() {
        if (this.needsUpdate) {
          this.$inertia.put(
            route('properties.update', this.property.id),
            {
              name: this.property.name,
              api_identifier: this.property.api_identifier,
            },
            { preserveState: false },
          )
        }
      },
      verifyProperty() {
        this.$modal.show(
          VerifyPropertyModal,
          {
            property: this.property,
          },
          {
            height: 'auto',
            scrollable: true,
            adaptive: true,
            classes: 'bg-transparent',
          },
        )
      },
    },
  }
</script>
