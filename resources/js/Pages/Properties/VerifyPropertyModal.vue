<template>
  <div class="bg-white rounded-lg flex flex-col items-center justify-center">
    <div class="bg-white w-full py-4 px-4 shadow sm:rounded-lg sm:px-10">
      <h2 class="text-center text-lg leading-9 font-bold text-gray-900">
        Verify Ownership Of
        <span class="text-brand-light">{{ property.api_identifier }}</span>
      </h2>
      <div class="flex flex-col items-center shadow-sm">
        <div class="px-4 py-6 rounded-t-lg border border-gray-300">
          <p class="text-brand-dark text-2xl font-medium tracking-tight">
            Step 1
          </p>
          <p class="mt-4">
            Connect your MINDBODY API to QuickerNotes by clicking on the
            activate button below or manually copy pasting the activation code.
          </p>
          <div class="mt-4 text-center">
            <span class="relative z-0 inline-flex shadow-sm">
              <a
                type="button"
                target="_blank"
                :href="property.activation_link"
                class="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm leading-5 font-medium text-blue-700 hover:text-blue-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-blue-700 transition ease-in-out duration-150"
              >
                Activate
              </a>
              <a
                type="button"
                href="https://support.mindbodyonline.com/s/article/Setting-up-an-API-integration?language=en_US"
                target="_blank"
                class="-ml-px relative inline-flex items-center px-4 py-2 rounded-r-md border border-gray-300 bg-white text-sm leading-5 font-medium text-gray-700 hover:text-gray-500 focus:z-10 focus:outline-none focus:border-blue-300 focus:shadow-outline-blue active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150"
              >
                Learn More
              </a>
            </span>
          </div>
          <div class="flex justify-center">
            <span
              class="mt-4 p-1 inline-block border border-brand-light rounded text-center text-brand-light font-medium text-sm tracking-wide"
              v-text="property.activation_code"
            ></span>
          </div>
        </div>
        <div class="px-4 py-6 rounded-b-lg border border-gray-300">
          <p class="text-brand-dark text-2xl font-medium tracking-tight">
            Step 2
          </p>

          <p class="mt-4">
            Enter the MINDBODY Owner credentials for the site to verify that you
            have permission to add it here.
          </p>
          <div
            class="bg-white rounded flex flex-col items-center justify-center"
          >
            <div class="bg-white w-full py-4 px-4 sm:px-10">
              <form @submit.prevent="verify" class="mt-4">
                <div>
                  <label
                    for="username"
                    class="block text-sm font-medium leading-5 text-gray-700"
                  >
                    Site Owner Username
                  </label>
                  <div class="mt-1 rounded-md shadow-sm">
                    <input
                      id="username"
                      type="text"
                      v-model="form.username"
                      required
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    />
                    <div v-if="$page.errors.username" class="form-error">
                      {{ $page.errors.username[0] }}
                    </div>
                  </div>
                </div>

                <div class="mt-6">
                  <label
                    for="password"
                    class="block text-sm font-medium leading-5 text-gray-700"
                  >
                    Site Owner Password
                  </label>
                  <div class="mt-1 rounded-md shadow-sm">
                    <input
                      id="password"
                      type="password"
                      v-model="form.password"
                      required
                      class="appearance-none block w-full px-3 py-2 border border-gray-300 rounded-md placeholder-gray-400 focus:outline-none focus:shadow-outline-blue focus:border-blue-300 transition duration-150 ease-in-out sm:text-sm sm:leading-5"
                    />
                  </div>
                  <div v-if="$page.errors.password" class="form-error">
                    {{ $page.errors.password[0] }}
                  </div>
                </div>

                <div class="mt-6">
                  <div class="block w-full rounded-md shadow-sm">
                    <loading-button
                      :loading="isLoading"
                      type="submit"
                      class="w-full flex justify-center py-2 px-4 border border-transparent text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-500 focus:outline-none focus:border-blue-700 focus:shadow-outline-blue active:bg-blue-700 transition duration-150 ease-in-out"
                    >
                      Verify
                    </loading-button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
  import LoadingButton from '../Shared/LoadingButton'

  export default {
    name: 'VerifyPropertyModal',
    components: { LoadingButton },
    props: ['property'],
    data() {
      return {
        isLoading: false,
        form: {
          api_identifier: this.property.api_identifier,
          username: null,
          password: null,
        },
      }
    },
    methods: {
      verify() {
        this.isLoading = true
        this.$inertia
          .post(route('mindbody.verify-ownership'), this.form)
          .then((res) => {
            this.isLoading = false
            this.$emit('close')
          })
      },
    },
  }
</script>

<style scoped></style>
