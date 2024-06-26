<template>
  <modal
    name="image-manager"
    class="rounded-lg"
    :scrollable="true"
    :draggable="false"
    transition="pop-out"
    :width="modalWidth"
    :min-height="600"
    :height="images.length ? 'auto' : 600"
  >
    <div class="p-10">
      <div class="flex justify-between items-center border-b mb-6">
        <div class="flex items-center">
          <h1 class="text-2xl tracking-wide mr-4">Image Manager</h1>
          <image-uploader :classes="'inline'" />
        </div>
        <span
          class="btn btn-gray btn-sm mb-1"
          @click="$modal.hide('image-manager')"
        >
          <icon
            @click="$modal.hide('image-manager')"
            class="h-5 w-5 text-gray-900 fill-current"
            name="close"
          />
        </span>
      </div>
      <div class="flex flex-wrap justify-between">
        <div
          v-for="image in images"
          :key="image.id"
          class="rounded shadow mb-4 overflow-hidden hover:opacity-75 cursor-pointer"
        >
          <img
            @click.prevent="useImage(image)"
            :src="image.signed_url"
            alt=""
            class="w-auto h-32"
          />
        </div>
      </div>
    </div>
  </modal>
</template>
<script>
  const MODAL_WIDTH = 960
  import Icon from './Icon'
  import ImageUploader from './ImageUploader'

  export default {
    name: 'ImageManager',
    props: {
      images: {
        type: Array,
        default: [],
      },
    },
    components: {
      Icon,
      ImageUploader,
    },
    data() {
      return {
        modalWidth: MODAL_WIDTH,
        showUploader: false,
      }
    },
    created() {
      this.modalWidth =
        window.innerWidth < MODAL_WIDTH ? MODAL_WIDTH / 2 : MODAL_WIDTH
    },
    methods: {
      useImage(image) {
        this.$emit('use-image', image)

        this.$modal.hide('image-manager')
      },
    },
  }
</script>
<style>
  .pop-out-enter-active,
  .pop-out-leave-active {
    transition: all 0.5s;
  }
  .pop-out-enter,
  .pop-out-leave-active {
    opacity: 0;
    transform: translateY(24px);
  }
</style>
