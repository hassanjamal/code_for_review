<template>
  <div style="font-size: 60%;">
    <modal
      :classes="'mt-16'"
      name="note-history-modal"
      :clickToClose="true"
      :scrollable="true"
      height="auto"
      :adaptive="true"
      :pivotY="0.1"
      @before-open="beforeOpen"
      @before-close="beforeClose"
    >
      <div class="grid grid-cols-4 gap-2 bg-gray-100 shadow-md relative py-2">
        <div class="ml-2">
          <button class="btn btn-gray btn-sm" @click="copy">
            Copy Note
          </button>
        </div>
        <div class="flex flex-wrap justify-center col-span-2">
          <div v-for="(link, key) in links">
            <div
              v-if="link.url === null"
              :key="key"
              class="mr-1 px-2 py-1 text-sm border rounded text-grey"
              :class="{ 'ml-auto': link.label === 'Next' }"
            >
              {{ link.label }}
            </div>
            <button
              v-else
              :key="key"
              class="mr-1 px-2 py-1 text-sm border rounded hover:bg-white focus:border-brand-light focus:text-brand-light"
              :class="{
                'bg-white': link.active,
                'ml-auto': link.label === 'Next',
              }"
              @click="getNotes(link.url)"
            >
              {{ link.label }}
            </button>
          </div>
        </div>
        <button
          class="absolute top-1 right-1"
          @click="$modal.hide('note-history-modal')"
        >
          <icon name="close" class="w-8 h-8" />
        </button>
      </div>
      <completed-note-view
        ref="note"
        class="h-full"
        v-if="noteInModal && appointmentOfNoteInModal && content"
        :show-print="false"
        :appointment="appointmentOfNoteInModal"
        :existing-note="noteInModal"
        :client="client"
        :content="content"
      />
      <div v-else>
        <p class="text-gray-500 p-4">
          There are no notes to display at this time.
        </p>
      </div>
    </modal>
  </div>
</template>
<script>
  import CompletedNoteView from '../ProgressNotes/Appointments/CompletedNoteView'
  import Icon from '../Shared/Icon'
  const axios = require('axios')

  export default {
    name: 'note-history-viewer',
    components: {
      Icon,
      CompletedNoteView,
    },
    data() {
      return {
        client: null,
        noteInModal: null,
        content: '',
        appointmentOfNoteInModal: null,
        copyToNoteAppointment: null,
        links: [],
      }
    },
    methods: {
      beforeOpen(event) {
        this.client = event.params.client
        this.copyToNoteAppointment = event.params.appointment
        this.getNotes()
      },

      beforeClose() {
        this.client = null
        this.noteInModal = null
        this.content = null
        this.appointmentOfNoteInModal = null
        this.copyToNoteAppointment = null
        this.links = []
      },

      copy() {
        this.$inertia.visit(
          route('appointment.progress-notes.create', {
            appointment: this.copyToNoteAppointment.id,
            copiedNoteId: this.noteInModal.id,
          }),
          {
            method: 'get',
            data: {},
            replace: false,
            preserveState: false,
            preserveScroll: false,
            only: [],
          },
        )

        this.$modal.hide('note-history-modal')
      },
      getNotes(url = null) {
        let endpoint = route('note-history', { clientId: this.client.id })
        if (url) {
          endpoint = url
        }

        axios.get(endpoint).then((res) => {
          let note = res.data.data[0]
          if (note) {
            this.noteInModal = note
            this.content = note.content
            this.appointmentOfNoteInModal = note.notable
            this.links = res.data.links
          }
        })
      },
    },
  }
</script>
