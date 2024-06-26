<template>
  <div class="flex cursor-pointer text-gray-600 text-sm">
    <button
      v-tooltip.top-start="'Your appointments'"
      class="border border-gray-200 rounded-l hover:bg-gray-200 p-1"
      v-bind:class="{
        'text-gray-800 bg-gray-200':
          value === $page.auth.user.api_id && value != null,
      }"
      @click="$emit('input', $page.auth.user.api_id)"
    >
      <svg
        class="inline w-3 h-4 fill-current"
        viewBox="0 0 12 10"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M4 0a2.5 2.5 0 100 5 2.5 2.5 0 000-5zM3 6a3 3 0 00-3 3 1 1 0 001 1h6a1 1 0 001-1 3 3 0 00-3-3H3z"
        ></path>
      </svg>
      Me
    </button>
    <button
      v-tooltip.top-start="'Everyone\'s appointments'"
      class="border border-gray-200 rounded-r hover:bg-gray-200 px-3 py-1"
      v-bind:class="{ 'text-gray-800 bg-gray-200': value == null }"
      @click="$emit('input', null)"
    >
      <svg
        class="w-3 h-4 fill-current"
        viewBox="0 0 12 10"
        xmlns="http://www.w3.org/2000/svg"
      >
        <path
          d="M1.5 2.5a2.5 2.5 0 115 0 2.5 2.5 0 01-5 0zM6.75 4.665c.47-.595.75-1.347.75-2.165 0-.818-.28-1.57-.75-2.165a2.5 2.5 0 110 4.33zM8.732 10A1.99 1.99 0 009 9a3.99 3.99 0 00-1.354-3H9a3 3 0 013 3 1 1 0 01-1 1H8.732zM3 6a3 3 0 00-3 3 1 1 0 001 1h6a1 1 0 001-1 3 3 0 00-3-3H3z"
        ></path>
      </svg>
    </button>
  </div>
</template>

<script>
  export default {
    name: 'StaffFilter',
    props: ['value'],
    data() {
      return {
        showMenu: false,
      }
    },
  }
</script>

<style scoped></style>
