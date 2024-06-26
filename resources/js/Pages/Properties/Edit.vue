<template>
  <div>
    <h1 class="mb-2 font-bold text-2xl">
      <inertia-link
        class="text-blue-700 hover:text-blue-800"
        :href="route('properties.index')"
        >Properties
      </inertia-link>
      <span class="text-blue-400 font-medium">/</span>
      {{ form.name }}
    </h1>
    <div
      class="flex inline-flex items-center border border-green-500 rounded-lg bg-white px-2 py-1"
    >
      <div>Site ID {{ form.api_identifier }}</div>
      <div
        v-if="property.verified"
        class="flex text-green-500 items-center ml-4"
      >
        <svg
          xmlns="http://www.w3.org/2000/svg"
          viewBox="0 0 24 24"
          class="icon-bolt h-6 w-6 mr-2"
        >
          <circle cx="12" cy="12" r="10" class="fill-current text-green-500" />
          <path
            class="fill-current text-white"
            d="M14 10h2a1 1 0 0 1 .81 1.58l-5 7A1 1 0 0 1 10 18v-4H8a1 1 0 0 1-.81-1.58l5-7A1 1 0 0 1 14 6v4z"
          />
        </svg>
        Verified
      </div>
    </div>
    <!--    <trashed-message v-if="property.deleted_at" class="mb-6" @restore="restore">-->
    <!--      This property has been deleted.-->
    <!--    </trashed-message>-->
    <locations-table :locations="locations" />
  </div>
</template>

<script>
  import Icon from '../Shared/Icon'
  import Layout from '../Shared/Layout'
  import LoadingButton from '../Shared/LoadingButton'
  import TextInput from '../Shared/TextInput'
  import TrashedMessage from '../Shared/TrashedMessage'
  import LocationsTable from './LocationsTable'

  export default {
    metaInfo: { title: 'Properties' },
    layout: (h, page) => h(Layout, [page]),
    components: {
      Icon,
      Layout,
      LoadingButton,
      TextInput,
      TrashedMessage,
      LocationsTable,
    },
    props: {
      property: Object,
      locations: {
        type: Array,
        default: [],
      },
      errors: {
        type: Object,
        default: () => ({}),
      },
    },
    remember: 'form',
    data() {
      return {
        sending: false,
        verifying: false,
        form: {
          name: this.property.name,
          api_identifier: this.property.api_identifier,
        },
      }
    },
    methods: {
      submit() {
        this.sending = true
        this.$inertia
          .put(route('properties.update', this.property.id), this.form)
          .then(() => (this.sending = false))
      },
      destroy() {
        // if (confirm('Are you sure you want to delete this organization?')) {
        //   // this.$inertia.delete(this.route('organizations.destroy', this.organization.id))
        // }
      },
      restore() {
        // if (confirm('Are you sure you want to restore this organization?')) {
        //   this.$inertia.put(this.route('organizations.restore', this.organization.id))
        // }
      },

      verifyProperty() {
        this.verifying = true

        this.$inertia
          .post(route('mindbody.verify-ownership'), {
            api_identifier: this.property.api_identifier,
          })
          .then(() => {
            this.verifying = false
          })
      },
    },
  }
</script>
