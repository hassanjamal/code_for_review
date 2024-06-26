<template>
  <on-click-outside :do="close">
    <div class="relative" v-if="$page.auth.user">
      <button @click="isOpen = !isOpen">
        <icon name="user" class="fill-current h-8 w-8 text-blue-900" />
      </button>
      <div
        v-show="isOpen"
        class="absolute right-0 py-2 mt-2 w-48 bg-white rounded-lg shadow-xl"
      >
        <a
          class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white"
          :href="route('settings')"
        >
          Settings
        </a>
        <inertia-link
          class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white"
          :href="route('notifications.index')"
        >
          Notifications
        </inertia-link>
        <inertia-link
          class="block px-4 py-2 text-gray-800 hover:bg-blue-500 hover:text-white"
          :href="route('logout')"
          method="POST"
        >
          Logout
        </inertia-link>
      </div>
    </div>
  </on-click-outside>
</template>

<script>
  import Icon from './Icon'
  import OnClickOutside from './OnClickOutside'
  export default {
    components: {
      OnClickOutside,
      Icon,
    },
    data() {
      return {
        isOpen: false,
      }
    },
    methods: {
      close() {
        this.isOpen = false
      },
    },
  }
</script>
