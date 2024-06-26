<template>
  <div class="flex items-center">
    <div class="flex w-full bg-white shadow rounded">
      <dropdown
        v-if="filtered"
        class="px-4 md:px-6 rounded-l border-r bg-gray-600 text-white focus:border-white focus:shadow-outline focus:z-10"
        placement="bottom-start"
      >
        <div class="flex items-baseline">
          <span class="text-white hidden md:inline">Filters</span>
          <svg
            class="w-2 h-2 fill-current text-white ml-2"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 961.243 599.998"
          >
            <path
              d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"
            />
          </svg>
        </div>
        <div
          slot="dropdown"
          class="mt-2 px-4 py-6 w-screen shadow-lg bg-white rounded"
          :style="{ maxWidth: `${maxWidth}px` }"
        >
          <slot />
        </div>
      </dropdown>
      <dropdown
        v-else
        class="px-4 md:px-6 rounded-l border-r hover:bg-gray-100 focus:border-white focus:shadow-outline focus:z-10"
        placement="bottom-start"
      >
        <div class="flex items-baseline">
          <span class="text-grey-900 hidden lg:inline">Filters</span>
          <svg
            class="w-2 h-2 fill-current text-gray-700 ml-2"
            xmlns="http://www.w3.org/2000/svg"
            viewBox="0 0 961.243 599.998"
          >
            <path
              d="M239.998 239.999L0 0h961.243L721.246 240c-131.999 132-240.28 240-240.624 239.999-.345-.001-108.625-108.001-240.624-240z"
            />
          </svg>
        </div>
        <div
          slot="dropdown"
          class="mt-2 px-4 py-6 w-screen shadow-lg bg-white rounded"
          :style="{ maxWidth: `${maxWidth}px` }"
        >
          <slot />
        </div>
      </dropdown>
      <input
        class="relative w-full px-6 py-3 rounded-r focus:shadow-outline"
        autocomplete="off"
        type="text"
        name="search"
        placeholder="Search Clients…"
        :value="value"
        @input="$emit('input', $event.target.value)"
      />
    </div>
    <button
      class="ml-3 text-sm text-gray-700 hover:text-gray-800 focus:text-blue-700"
      type="button"
      @click="$emit('reset')"
    >
      Reset
    </button>
  </div>
</template>

<script>
  import Dropdown from './Dropdown'

  export default {
    components: {
      Dropdown,
    },
    props: {
      value: String,
      filtered: {
        type: Boolean,
        default: false,
      },
      maxWidth: {
        type: Number,
        default: 300,
      },
    },
  }
</script>
