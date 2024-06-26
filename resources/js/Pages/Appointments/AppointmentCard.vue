<template>
  <div class="rounded shadow-md p-2 flex mt-4" :class="statusClass">
    <div
      class="w-20 h-18 overflow-hidden mr-3 -m-2 rounded-l flex justify-center items-center"
    >
      <img
        class="rounded h-14 w-auto"
        v-if="client.photo_url"
        :src="client.photo_url"
        alt=""
      />
      <div v-else class="h-12 w-14 flex justify-center items-center px-5">
        <icon
          name="user"
          :class="'w-8 h-8 fill-current'"
          primary="text-gray-500"
          secondary="text-gray-800"
        />
      </div>
    </div>

    <div class="w-100 mr-2 pr-2 text-sm flex-grow-1">
      <p>
        <inertia-link
          class="font-bold text-base hover:underline"
          :href="route('clients.show', client.id)"
        >
          {{ client.full_name }}
        </inertia-link>
        {{ appointment.service_name }}
      </p>
      <p class="mt-1">
        {{ appointment.formatted_time
        }}<span v-if="appointment.room_name">
          - {{ appointment.room_name }}</span
        >
      </p>
      <p class="mt-1">{{ staff.full_name }}</p>
    </div>

    <div class="flex flex-grow justify-between">
      <div>
        <alerts :alerts="alerts" :client-id="client.id" />
        <div class="mt-3 flex justify-start items-center">
          <div>
            <button
              @click="
                $modal.show('note-history-modal', {
                  client: client,
                  appointment: appointment,
                })
              "
              class="text-gray-900 hover:text-gray-800"
            >
              History
            </button>
          </div>
          <div
            class="cursor-pointer ml-2"
            @click="
              $modal.show('appointment-notes-modal', {
                note: appointment.notes,
                appointmentId: appointment.id,
              })
            "
          >
            <span
              v-if="appointment.notes.length"
              class="h-3 w-3 inline-block rounded-full bg-brand-light"
            />
            <button class="text-gray-900 hover:text-gray-800">
              Note
            </button>
          </div>
        </div>
      </div>

      <div class="flex items-center">
        <progress-note-avatar-display
          v-if="userCanViewNotes"
          :visit="appointment"
        />
      </div>
    </div>
  </div>
</template>

<script>
  import Icon from '../Shared/Icon'
  import Alerts from '../Shared/Alerts'
  import ProgressNoteAvatarDisplay from '../Clients/ProgressNoteAvatarDisplay'

  export default {
    name: 'AppointmentCard',
    components: {
      ProgressNoteAvatarDisplay,
      Icon,
      Alerts,
    },
    props: ['appointment'],
    data() {
      return {
        location: this.appointment.location,
        client: this.appointment.client,
        staff: this.appointment.staff,
        alerts: this.appointment.client.alerts,
      }
    },
    computed: {
      userCanViewNotes() {
        return (
          this.$page.auth.user.permissions.filter((permission) => {
            return ['notes:view-all', 'notes:view-own'].includes(permission)
          }).length > 0
        )
      },
      statusClass() {
        switch (this.appointment.status.toLowerCase()) {
          case 'booked':
            return 'booked'
          case 'completed':
            return 'completed'
          case 'arrived':
            return 'arrived'
          case 'noshow':
            return 'missed'
          default:
            return 'booked'
        }
      },
    },
    methods: {
      tooltip(content, placement = 'top') {
        return {
          content,
          placement: 'top',
          delay: {
            show: 1000,
            hide: 200,
          },
        }
      },
    },
  }
</script>

<style scoped>
  .booked {
    @apply bg-gray-300;
  }

  .arrived {
    @apply bg-blue-300;
  }

  .completed {
    @apply bg-green-300;
  }

  .missed {
    @apply bg-red-300;
  }
  .v--modal-overlay[data-modal='notes'] {
    background: transparent;
  }
</style>
