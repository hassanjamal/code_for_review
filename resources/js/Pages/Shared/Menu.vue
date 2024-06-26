<template>
  <div class="text-sm">
    <div class="mb-4">
      <inertia-link
        class="flex flex-col justify-center items-center py-3"
        :href="route('dashboard')"
      >
        <icon
          name="home"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="isUrl('app') ? 'text-white' : 'text-gray-500'"
          :secondary="isUrl('app') ? 'text-blue-600' : 'text-gray-600'"
        />
        <div :class="isUrl('app') ? 'text-white' : 'text-gray-500'">Home</div>
      </inertia-link>
    </div>
    <div v-if="hasPermissionToViewNavLink('properties')" class="mb-4">
      <inertia-link
        class="flex flex-col justify-center items-center py-3"
        :href="route('properties.index')"
      >
        <icon
          name="property"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="isUrl('app/properties') ? 'text-white' : 'text-gray-500'"
          :secondary="
            isUrl('app/properties') ? 'text-blue-600' : 'text-gray-600'
          "
        />
        <div :class="isUrl('app/properties') ? 'text-white' : 'text-gray-500'">
          Properties
        </div>
      </inertia-link>
    </div>
    <div v-if="hasPermissionToViewNavLink('appointments')" class="mb-4">
      <inertia-link
        class="flex flex-col justify-center items-center py-3"
        :href="route('appointments.index')"
      >
        <icon
          name="calendar-date"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="isUrl('app/appointments') ? 'text-white' : 'text-gray-500'"
          :secondary="
            isUrl('app/appointments') ? 'text-blue-600' : 'text-gray-600'
          "
        />
        <div
          :class="isUrl('app/appointments') ? 'text-white' : 'text-gray-500'"
        >
          Appointments
        </div>
      </inertia-link>
    </div>
    <div v-if="hasPermissionToViewNavLink('clients')" class="mb-4">
      <inertia-link
        class="flex flex-col justify-center items-center py-3"
        :href="route('clients.index')"
      >
        <icon
          name="user-group"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="isUrl('app/clients') ? 'text-white' : 'text-gray-500'"
          :secondary="isUrl('app/clients') ? 'text-blue-600' : 'text-gray-600'"
        />
        <div :class="isUrl('app/clients') ? 'text-white' : 'text-gray-500'">
          Clients
        </div>
      </inertia-link>
    </div>
    <div v-if="hasPermissionToViewNavLink('notes')" class="mb-4">
      <!-- TODO change permissions -->
      <inertia-link
        class="flex flex-col justify-center items-center py-3"
        :href="route('progress-notes.index')"
      >
        <icon
          name="notes"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="
            isUrl('app/progress-notes') ? 'text-white' : 'text-gray-500'
          "
          :secondary="
            isUrl('app/progress-notes') ? 'text-blue-600' : 'text-gray-600'
          "
        />
        <div
          :class="isUrl('app/progress-notes') ? 'text-white' : 'text-gray-500'"
        >
          Chart Notes
        </div>
      </inertia-link>
    </div>
    <div v-if="hasPermissionToViewNavLink('templates')" class="mb-4">
      <!-- TODO change permissions -->
      <inertia-link
        class="flex flex-col justify-center items-center py-3 hover:text-white"
        :href="route('templates.index')"
      >
        <icon
          name="template"
          class="w-6 h-6 fill-current hover:text-white"
          :primary="isUrl('app/templates') ? 'text-white' : 'text-gray-500'"
          :secondary="
            isUrl('app/templates') ? 'text-blue-600' : 'text-gray-600'
          "
        />
        <div :class="isUrl('app/templates') ? 'text-white' : 'text-gray-500'">
          Templates
        </div>
      </inertia-link>
    </div>
  </div>
</template>

<script>
  import Icon from './Icon'
  export default {
    components: {
      Icon,
    },
    props: {
      url: String,
    },
    methods: {
      isUrl(...urls) {
        if (urls[0] === 'app') {
          return this.url === 'app'
        }
        return urls.filter((url) => this.url.startsWith(url)).length
      },
      hasPermissionToViewNavLink(link) {
        let user = this.$page.auth.user

        if (user) {
          return this.$page.auth.user.nav_permissions[link]
        }
      },
    },
  }
</script>
