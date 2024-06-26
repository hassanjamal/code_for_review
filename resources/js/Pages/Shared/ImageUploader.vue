<template>
  <on-click-outside :do="close">
    <div class="relative" :class="styles">
      <button class="" @click="open" v-tooltip:top="'Add image'">
        <icon
          name="photo"
          class="h-8 w-8 fill-current"
          :primary="'text-gray-400'"
          :secondary="'text-gray-700'"
        />
      </button>
      <div
        v-show="isOpen"
        class="absolute z-10 left-0 bg-white rounded border border-gray-500 bg-gray-100 shadow-lg p-10"
        style="width: 600px;"
      >
        <div class="h-full flex flex-col justify-center items-center">
          <input
            class="hidden"
            type="file"
            @change="onFileSelected"
            ref="fileInput"
          />
          <div class="flex justify-center items-center w-full no-wrap mb-6">
            <button
              class="btn btn-gray btn-sm mr-4"
              @click="$refs.fileInput.click()"
            >
              Choose File
            </button>
            <span
              class="text-gray-700"
              v-text="fileName ? fileName : 'No file chosen'"
            ></span>
          </div>
          <div v-show="uploadProgress">
            <p>
              Upload Progress -
              <span class="text-green-600 italic">{{ uploadProgress }} %</span>
            </p>
          </div>
          <button
            class="self-end btn btn-black"
            @click="onUpload"
            :disabled="!selectedFile"
          >
            Upload
          </button>
        </div>
      </div>
    </div>
  </on-click-outside>
</template>

<script>
  import OnClickOutside from './OnClickOutside'
  import Icon from './Icon'
  import Vapor from 'laravel-vapor'

  export default {
    name: 'ImageUploader',
    props: ['styles'],
    components: {
      OnClickOutside,
      Icon,
    },
    data() {
      return {
        isOpen: false,
        selectedFile: null,
        fileName: null,
        uploadProgress: null,
      }
    },
    methods: {
      open() {
        this.isOpen = true
      },
      onFileSelected(event) {
        this.readyToUpload = true
        this.fileName = event.target.files[0].name
        this.selectedFile = event.target.files[0]
      },
      onUpload() {
        if (!this.selectedFile) {
          return
        }

        Vapor.store(this.selectedFile, {
          progress: (progress) => {
            this.uploadProgress = Math.round(progress * 100)
          },
        }).then((response) => {
          this.$inertia.post(route('image-manager.store'), {
            uuid: response.uuid,
            key: response.key,
            bucket: response.bucket,
            name: this.selectedFile.name,
            content_type: this.selectedFile.type,
          })
        })
      },
      close() {
        this.selectedFile = null
        this.fileName = null
        this.$refs.fileInput.value = null
        this.uploadProgress = null
        this.isOpen = false
      },
    },
  }
</script>
