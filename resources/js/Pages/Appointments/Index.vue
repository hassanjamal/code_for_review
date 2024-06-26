<template>
  <div>
    <div
      class="flex flex-col flex-no-wrap justify-center md:flex-row md:justify-between items-center"
    >
      <div class="mb-3">
        <search-filter
          v-model="form.search"
          class="w-full max-w-sm mr-4"
          :filtered="isFiltering"
          @reset="reset"
        >
          <label class="block text-grey-darkest">Status:</label>
          <select v-model="form.status" class="mt-1 w-full form-select">
            <option :value="null">Everything</option>
            <option value="booked">Booked</option>
            <option value="arrived">Arrived</option>
            <option value="arrived-booked">Arrived & Booked</option>
            <option value="completed">Completed</option>
            <option value="no-show">No Show</option>
          </select>

          <label class="block text-grey-darkest mt-4">Locations:</label>
          <select v-model="form.location" class="mt-1 w-full form-select">
            <option :value="null">All Locations</option>
            <option
              v-for="location in visibleLocations"
              :key="location.id"
              :value="location.id"
            >
              {{ location.name }}
            </option>
          </select>
        </search-filter>
      </div>
      <div class="flex justify-center items-center">
        <div
          class="btn btn-gray btn-sm mr-1 cursor-pointer"
          @click="subDay()"
          v-tooltip="'Previous Day'"
        >
          prev
        </div>
        <input
          class="rounded border border-gray-200 pl-2 mr-1"
          type="date"
          v-model="form.date"
        />
        <div
          class="btn btn-gray btn-sm mr-6 cursor-pointer"
          @click="addDay()"
          v-tooltip.top-start="'Next Day'"
        >
          next
        </div>
        <staff-filter v-model="form.staff" />
      </div>
    </div>

    <div v-if="appointments.data.length" class="container mx-auto">
      <pagination :links="appointments.links" />
      <div class="overflow-y-scroll">
        <appointment-card
          style="min-width: 970px;"
          v-for="appointment in appointments.data"
          :key="appointment.id"
          :appointment="appointment"
        />
      </div>
      <pagination :links="appointments.links" />
      <note-history-viewer />
      <appointment-notes-modal />
    </div>
    <div v-else class="mt-4">
      <p class="mb-4 text-lg font-bold">No appointments to display.</p>
      <p class="mb-2" v-if="form.status">
        You are currently filtering results to only show appointments with the
        status of <span class="capitalize font-bold">{{ displayStatus }}</span
        >.
      </p>
      <p v-if="form.location">
        You are currently filtering results to only show appointments from
        <span class="capitalize font-bold">{{ displayLocation }}</span
        >.
      </p>
    </div>
  </div>
</template>

<script>
  import _ from 'lodash'
  import AppointmentCard from './AppointmentCard'
  import { addDays, format, parseISO, subDays } from 'date-fns'
  import Icon from '../Shared/Icon'
  import Layout from '../Shared/Layout'
  import Pagination from '../Shared/Pagination'
  import SearchFilter from '../Shared/SearchFilter'
  import StaffFilter from '../Shared/StaffFilter'
  import NoteHistoryViewer from './NoteHistoryViewer'
  import AppointmentNotesModal from './AppointmentNotesModal'

  export default {
    metaInfo: { title: 'Appointments' },
    layout: (h, page) => h(Layout, [page]),
    props: {
      appointments: Object,
      filters: Object,
      visibleLocations: Array,
    },
    data() {
      return {
        form: {
          search: this.filters.search,
          status: this.filters.status,
          location: this.filters.location,
          date: this.filters.date,
          staff: this.filters.staff,
        },
      }
    },
    components: {
      Pagination,
      SearchFilter,
      StaffFilter,
      Icon,
      AppointmentCard,
      NoteHistoryViewer,
      AppointmentNotesModal,
    },
    watch: {
      form: {
        handler: _.throttle(function () {
          let query = _.pickBy(this.form)
          this.$inertia.replace(
            this.route(
              'appointments.index',
              Object.keys(query).length ? query : { remember: 'forget' },
            ),
          )
        }, 150),
        deep: true,
      },
    },
    computed: {
      isFiltering() {
        return (
          Boolean(this.form.status) ||
          Boolean(this.form.search) ||
          Boolean(this.form.location)
        )
      },
      displayStatus() {
        let status = this.form.status

        if (status === 'arrived-booked') {
          return 'Arrived & Booked'
        }
        if (status === 'no-show') {
          return 'No Show'
        }

        return status
      },
      displayLocation() {
        return this.visibleLocations.filter((visibleLocation) => {
          return visibleLocation.id == this.form.location
        })[0].name
      },
    },

    methods: {
      subDay() {
        this.form.date = format(
          subDays(parseISO(this.form.date), 1),
          'yyyy-MM-dd',
        )
      },
      addDay() {
        this.form.date = format(
          addDays(parseISO(this.form.date), 1),
          'yyyy-MM-dd',
        )
      },
      reset() {
        this.form.status = null
        this.form.search = null
        this.form.location = null
      },
    },
  }
</script>

<style scoped></style>
