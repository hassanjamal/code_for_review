<template>
  <div class="flex items-start">
    <div class="text-grey-900 flex items-center">
      <ul class="flex flex-wrap">
        <li
          class="mr-1 p-1 text-xs bg-white rounded border border-red-800"
          v-for="alert in alerts"
          :key="alert.id"
          v-tooltip:top="alert.text"
        >
          {{ truncate(alert.text) }}
          <span @click="deleteAlert(alert.id)">
            <icon
              name="close-circle"
              primary="text-red-800"
              secondary="text-white"
              class="cursor-pointer inline fill-current w-4 h-4 ml-1"
            />
          </span>
        </li>
      </ul>

      <button
        v-if="!showInput"
        v-tooltip:top="'New Alert'"
        @click.prevent="open"
      >
        <icon name="add" class="w-5 h-5" />
      </button>

      <form v-else-if="showInput" @submit.prevent="saveAlert">
        <div class="flex justify-start items-end">
          <input
            type="text"
            v-model="text"
            ref="input"
            class="rounded-l h-5 pl-1"
          />
          <button
            class="mr-2 h-5 px-3 bg-gray-700 text-white font-medium rounded-r"
            :disabled="!text.length"
          >
            save
          </button>
          <span
            @click="close"
            class="cursor-pointer text-xl font-extrabold"
            v-tooltip:top="'cancel'"
          >
            <icon name="close" class="w-5 h-5" />
          </span>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import Icon from './Icon'
  export default {
    name: 'Alerts',
    components: {
      Icon,
    },
    props: {
      alerts: {
        type: Array,
        required: true,
      },
      clientId: {
        type: String,
        required: true,
      },
      textLength: {
        type: Number,
        default: 25,
      },
    },
    data() {
      return {
        text: '',
        showInput: false,
      }
    },
    created() {
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && this.showInput) {
          this.close()
        }
      })
    },
    methods: {
      truncate(text) {
        const textLength = this.textLength

        if (text.length > textLength) {
          return text.substring(0, textLength) + '...'
        }

        return text
      },
      saveAlert() {
        if (!this.text.length) {
          return
        }
        this.$inertia.post(
          route('alerts.store'),
          {
            text: this.text,
            clientId: this.clientId,
          },
          {
            preserveState: false,
            preserveScroll: true,
          },
        )

        this.text = ''
        this.showInput = false
      },
      deleteAlert(alertId) {
        this.$inertia.delete(route('alerts.delete', alertId), {
          preserveState: false,
          preserveScroll: true,
        })
      },
      open() {
        this.showInput = true
        this.$nextTick(() => this.$refs.input.focus())
      },
      close() {
        this.text = ''
        this.showInput = false
      },
    },
  }
</script>

<style scoped></style>
