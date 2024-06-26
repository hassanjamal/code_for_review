<template>
  <div class="hidden md:flex md:flex-shrink-0">
    <div class="flex flex-col w-56">
      <div class="flex items-center h-16 flex-shrink-0 px-4 bg-gray-900">
        <img
          class="h-8 w-auto"
          :src="$page.logos.quickernotes_logo_white"
          alt="QuickerNotes"
        />
      </div>
      <div class="h-0 flex-1 flex flex-col overflow-y-auto">
        <nav class="flex-1 px-2 py-4 bg-gray-800">
          <inertia-link
            :href="route('dashboard')"
            class="group flex items-center px-2 py-2 text-sm leading-5 font-medium text-white rounded-md focus:outline-none focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-300 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"
              />
            </svg>
            Dashboard
          </inertia-link>
          <inertia-link
            :href="route('properties.index')"
            v-if="hasPermissionToViewNavLink('properties')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/properties') }"
          >
            <svg
              class="mr-4 h-6 w-6 text-gray-300 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 12l9-9 9 9M5 10v10a1 1 0 001 1h3a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1h3a1 1 0 001-1V10M9 21h6"
              />
            </svg>
            Properties
          </inertia-link>
          <inertia-link
            :href="route('appointments.index')"
            v-if="hasPermissionToViewNavLink('appointments')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/appointments') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"
              />
            </svg>
            Appointments
          </inertia-link>
          <inertia-link
            :href="route('clients.index')"
            v-if="hasPermissionToViewNavLink('clients')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/clients') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
              />
            </svg>
            Clients
          </inertia-link>
          <inertia-link
            :href="route('progress-notes.index')"
            v-if="hasPermissionToViewNavLink('notes')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/progress-notes') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
              />
            </svg>
            Chart Notes
          </inertia-link>
          <inertia-link
            :href="route('templates.index')"
            v-if="hasPermissionToViewNavLink('templates')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/templates') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                class="stroke-2"
                d="M3 6l9 4v12l-9-4V6zm14-3v2c0 1.1-2.24 2-5 2s-5-.9-5-2V3c0 1.1 2.24 2 5 2s5-.9 5-2z"
              />
              <polygon class="stroke-2" points="21 6 12 10 12 22 21 18" />
            </svg>
            Templates
          </inertia-link>
          <!--                    <inertia-link href="#" class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150">-->
          <!--                        <svg class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150" stroke="currentColor" fill="none" viewBox="0 0 24 24">-->
          <!--                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>-->
          <!--                        </svg>-->
          <!--                        Reports-->
          <!--                    </inertia-link>-->
          <inertia-link
            :href="route('roles.index')"
            v-if="hasPermissionToViewNavLink('roles')"
            class="mt-1 group flex items-center px-2 py-2 text-sm leading-5 font-medium text-gray-300 rounded-md hover:text-white hover:bg-gray-700 focus:outline-none focus:text-white focus:bg-gray-700 transition ease-in-out duration-150"
            :class="{ 'bg-gray-900': isUrl('app/roles') }"
          >
            <svg
              class="mr-3 h-6 w-6 text-gray-400 group-hover:text-gray-300 group-focus:text-gray-300 transition ease-in-out duration-150"
              stroke="currentColor"
              fill="none"
              viewBox="0 0 24 24"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                stroke-width="2"
                d="M9 12L11 14L15 10M20.6179 5.98434C20.4132 5.99472 20.2072 5.99997 20 5.99997C16.9265 5.99997 14.123 4.84453 11.9999 2.94434C9.87691 4.84446 7.07339 5.99985 4 5.99985C3.79277 5.99985 3.58678 5.9946 3.38213 5.98422C3.1327 6.94783 3 7.95842 3 9.00001C3 14.5915 6.82432 19.2898 12 20.622C17.1757 19.2898 21 14.5915 21 9.00001C21 7.95847 20.8673 6.94791 20.6179 5.98434Z"
              />
            </svg>
            Roles & Permissions
          </inertia-link>
        </nav>
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    name: 'MainMenu',
    props: ['url'],
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
