<template>
  <modal
    name="appointment-notes-modal"
    :adaptive="true"
    :clickToClose="true"
    height="auto"
    @before-open="beforeOpen"
  >
    <form class="flex flex-no-wrap justify-center" @submit.prevent="saveData">
      <input
        type="text"
        class="w-full form-input rounded-r-none"
        v-model="note"
      />
      <LoadingButton class="btn btn-black rounded-l-none">Save</LoadingButton>
    </form>
  </modal>
</template>

<script>
  import LoadingButton from '../Shared/LoadingButton'
  export default {
    name: 'AppointmentNotesModal',
    components: { LoadingButton },
    data() {
      return {
        appointmentId: null,
        originalNote: '',
        note: '',
      }
    },
    methods: {
      beforeOpen(event) {
        this.appointmentId = event.params.appointmentId
        this.originalNote = event.params.note
        this.note = event.params.note
      },
      saveData() {
        if (this.originalNote !== this.note) {
          this.$inertia.post(
            route('appointments.update', { appointment: this.appointmentId }),
            {
              note: this.note,
              preserveState: false,
              preserveScroll: true,
              only: ['appointments'],
            },
          )

          this.$modal.hide('appointment-notes-modal')
        }
      },
    },
  }
</script>

<style scoped></style>
