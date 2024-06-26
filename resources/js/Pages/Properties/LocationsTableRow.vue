<template>
  <tr v-if="location" class="hover:bg-gray-100 focus:bg-gray-100">
    <td class="border-t px-6 py-4 focus:outline-none">
      {{ location.name }}
    </td>
    <td class="border-t px-6 py-4 focus:outline-none">
      {{ location.city }}
    </td>
    <td class="border-t px-6 py-4 focus:outline-none">
      {{ location.phone }}
    </td>
    <td class="border-t px-6 py-4 focus:outline-none">
      <p v-if="active">Subscribed</p>
      <p v-if="onGracePeriod" class="text-red-500 italic">
        Cancelled, expires in {{ expiresAt }}
      </p>
      <p v-if="notSubscribed">--</p>
    </td>
    <td class="border-t px-6 py-4 focus:outline-none">
      <form v-if="active" @submit.prevent="cancel">
        <loading-button
          :loading="cancelling"
          class="btn btn-sm btn-gray"
          type="submit"
          >Cancel</loading-button
        >
      </form>
      <form v-if="onGracePeriod" @submit.prevent="reactivate">
        <loading-button
          :loading="reactivating"
          class="btn btn-sm btn-gray"
          type="submit"
          >Reactivate</loading-button
        >
      </form>
      <form v-if="notSubscribed" @submit.prevent="subscribe">
        <loading-button
          :loading="subscribing"
          class="btn btn-sm btn-gray"
          type="submit"
          >Subscribe</loading-button
        >
      </form>
    </td>
  </tr>
</template>

<script>
  import { formatDistanceToNow, parseISO } from 'date-fns'
  import LoadingButton from '../Shared/LoadingButton'

  export default {
    props: {
      location: {
        type: Object,
        default: {},
      },
      subscription: {
        type: Object,
        default: {},
      },
    },

    data() {
      return {
        cancelling: false,
        subscribing: false,
        reactivating: false,
      }
    },

    components: {
      LoadingButton,
    },

    computed: {
      active() {
        return this.location.is_subscribed && !this.onGracePeriod
      },
      onGracePeriod() {
        return this.location.is_subscribed && this.subscription.ends_at
      },
      notSubscribed() {
        return !this.location.is_subscribed
      },
      expiresAt() {
        if (this.subscription && this.subscription.ends_at) {
          return formatDistanceToNow(parseISO(this.subscription.ends_at))
        }

        return ''
      },
    },

    methods: {
      reactivate() {
        this.reactivating = true

        this.$inertia
          .post(
            route('location-subscriptions.reactivate', {
              id: this.location.id,
            }),
          )
          .then(() => {
            this.reactivating = false
          })
      },
      subscribe() {
        this.subscribing = true

        this.$inertia
          .post(route('location-subscriptions.store'), {
            location_id: this.location.id,
          })
          .then(() => {
            this.subscribing = false
          })
      },
      cancel() {
        this.cancelling = true

        this.$inertia
          .delete(
            route('location-subscriptions.cancel', { id: this.location.id }),
          )
          .then(() => {
            this.cancelling = false
          })
      },
    },
  }
</script>

<style scoped></style>
