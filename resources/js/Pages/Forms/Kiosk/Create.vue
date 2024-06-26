<template>
  <div class="bg-blue-900 min-h-screen flex justify-center">
    <div class="container mx-auto py-24">
      <div class="mb-8 text-center text-white">
        <div class="flex justify-center">
          <a :href="route('home')">
            <img
              class="h-28 w-auto"
              :src="$page.logos.quickernotes_logo_white"
              alt="QuickerNotes"
            />
          </a>
        </div>
        <p class="text-2xl">
          Enter The Code To Access Intake Form
        </p>
      </div>

      <form @submit.prevent="submit" class="flex justify-center">
        <div class="w-full max-w-xl bg-white border rounded-b p-4 shadow-md">
          <div class="flex flex-wrap -mx-3">
            <div class="w-full px-3 mb-6 md:mb-0">
              <label
                class="block uppercase tracking-wide text-gray-700 text-xs font-bold mb-2"
                for="code"
              >
                Enter Code
              </label>
              <input
                class="appearance-none block w-full bg-gray-200 text-gray-700 border rounded py-3 px-4 mb-3 leading-tight focus:outline-none focus:bg-white"
                v-model="form.kioskCode"
                v-mask="'###-###'"
                id="code"
                type="text"
                placeholder="###-###"
              />
              <div class="italic text-red-600" v-if="$page.errors.kioskCode">
                {{ $page.errors.kioskCode[0] }}
              </div>
              <div class="italic text-red-600" v-if="$page.errors.invalidCode">
                {{ $page.errors.invalidCode[0] }}
              </div>
            </div>
          </div>

          <div class="flex -mx-3 mb-6">
            <div class="w-full px-3">
              <button
                type="submit"
                class="btn btn-black py-4 block w-full text-xl pl-10 pr-10 pt-4 pb-4 hover:bg-blue-800"
              >
                Submit
              </button>
            </div>
          </div>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import LoadingButton from '../../Shared/LoadingButton'

  export default {
    components: {
      LoadingButton,
    },
    metaInfo: { title: 'Kiosk' },
    props: [],
    data() {
      return {
        sending: false,
        form: {
          kioskCode: null,
        },
      }
    },

    methods: {
      submit() {
        this.sending = true
        this.$inertia.post(route('kiosk_form.store'), this.form).then(() => {
          this.sending = false
        })
      },
    },
  }
</script>

<style scoped></style>
