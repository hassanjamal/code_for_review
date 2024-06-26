<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-blue-600 hover:text-blue-800"
        :href="route('properties.index')"
        >Properties</inertia-link
      >
      <span class="text-blue-500 font-medium">/</span> Add New
    </h1>
    <div class="bg-white rounded shadow overflow-hidden max-w-lg">
      <form @submit.prevent="submit">
        <div class="p-8 -mr-6 -mb-8">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class="pr-6 pb-8 w-full"
            tabindex="0"
            autofocus
            label="Name"
          />
          <text-input
            v-model="form.api_identifier"
            :errors="$page.errors.api_identifier"
            class="pr-6 pb-8 w-full"
            label="Site ID"
          />
        </div>
        <div
          class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center"
        >
          <loading-button :loading="sending" class="btn btn-black" type="submit"
            >Add Property</loading-button
          >
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import TextInput from '../Shared/TextInput'
  import LoadingButton from '../Shared/LoadingButton'
  import Layout from '../Shared/Layout'
  export default {
    metaInfo: { title: 'Create Property' },
    layout: (h, page) => h(Layout, [page]),
    components: {
      TextInput,
      LoadingButton,
    },
    data() {
      return {
        sending: false,
        form: {
          name: null,
          api_identifier: null,
        },
      }
    },
    methods: {
      submit() {
        this.sending = true

        this.$inertia
          .post(route('mindbody.properties.store', this.form))
          .then((res) => {
            this.sending = false
          })
      },
    },
  }
</script>

<style scoped></style>
