<template>
  <div>
    <h1 class="mb-8 font-bold text-2xl">
      <inertia-link
        class="text-blue-700 hover:text-blue-800"
        :href="route('documents.index')"
        >Notes
      </inertia-link>
      <span class="text-blue-400 font-medium">/</span>
      {{ document.id }}
    </h1>

    <div class="bg-white rounded shadow overflow-hidden w-full">
      <div class="p-8 -mr-6 -mb-8">
        <text-input
          v-model="form.randomField"
          :errors="$page.errors.randomField"
          class="pr-6 pb-8 w-full"
          disabled
          label="Random Text Input"
        />
      </div>
      <form @submit.prevent="submit">
        <div
          class="px-8 py-4 bg-grey-lightest border-t border-grey-lighter flex justify-end items-center"
        >
          <loading-button :loading="sending" class="btn btn-black" type="submit"
            >Update Document
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import Icon from '../Shared/Icon'
  import Layout from '../Shared/Layout'
  import LoadingButton from '../Shared/LoadingButton'
  import TextInput from '../Shared/TextInput'

  export default {
    metaInfo: { title: 'Documents' },
    layout: (h, page) => h(Layout, [page]),
    components: {
      Icon,
      Layout,
      LoadingButton,
      TextInput,
    },
    props: {
      document: Object,
    },
    remember: 'form',
    data() {
      return {
        sending: false,
        form: {
          randomField: 'To Be Removed', // TODO change it later
        },
      }
    },
    methods: {
      submit() {
        this.sending = true
        this.$inertia
          .put(route('documents.update', this.document.id), this.form)
          .then(() => (this.sending = false))
      },
      destroy() {},
    },
  }
</script>
