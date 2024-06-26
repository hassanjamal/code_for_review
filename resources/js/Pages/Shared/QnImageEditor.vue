<template>
  <on-click-outside :do="close">
    <slide-up-fade-transition>
      <div
        v-show="show"
        class="flex justify-center mb-4 rounded-lg bg-gray-100 p-4 border border-gray-200 shadow-lg"
      >
        <div class="flex flex-col justify-center relative">
          <div class="flex">
            <input
              class="w-15 h-15 outline-none focus:outline-none border border-black mr-4"
              type="color"
              id="color"
              name="color"
              value="#000000"
              @change="updateColors"
            />

            <!-- Tool Bar Section -->
            <div class="w-full">
              <!-- Top Toolbar Row -->
              <div class="mb-2">
                <!-- Tool Picker -->
                <div class="mb-2 flex flex-no-wrap items-center">
                  <button
                    class="mr-8 text-gray-500 hover:text-gray-900"
                    :class="{ 'text-gray-900': activeTool === 'freeDrawing' }"
                    @click="setImageEditorMode('freeDrawing')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 20 20"
                      class="fill-current w-6 h-6"
                    >
                      <path
                        d="M13.5858 3.58579C14.3668 2.80474 15.6332 2.80474 16.4142 3.58579C17.1953 4.36683 17.1953 5.63316 16.4142 6.41421L15.6213 7.20711L12.7929 4.37868L13.5858 3.58579Z"
                      />
                      <path
                        d="M11.3787 5.79289L3 14.1716V17H5.82842L14.2071 8.62132L11.3787 5.79289Z"
                      />
                    </svg>
                  </button>
                  <button
                    class="mr-8 text-gray-500 hover:text-gray-900"
                    :class="{
                      'text-gray-900 font-bold': activeTool === 'text',
                    }"
                    @click="setImageEditorMode('text')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="fill-current w-4 h-4 font-medium"
                      viewBox="0 0 20 20"
                    >
                      <path d="M20,0V3H11V20H8V3H0V0Z" />
                    </svg>
                  </button>
                  <button
                    class="mr-8 text-gray-500 hover:text-gray-900"
                    :class="{ 'text-gray-900': activeTool === 'selectMode' }"
                    @click="setImageEditorMode('selectMode')"
                  >
                    <svg
                      xmlns="http://www.w3.org/2000/svg"
                      class="fill-current w-5 h-5"
                      viewBox="0 0 128 128"
                    >
                      <polygon
                        points="109.672,82.345 109.672,67.721 67.684,67.721 67.684,109.667 82.309,109.667
		63.982,127.993 45.654,109.667 60.279,109.667 60.279,67.721 18.328,67.721 18.328,82.345 0,64.019 18.328,45.693 18.328,60.316
		60.279,60.316 60.279,18.333 45.654,18.333 63.982,0.007 82.309,18.333 67.684,18.333 67.684,60.316 109.672,60.316
		109.672,45.693 128,64.019 	"
                      />
                    </svg>
                  </button>
                  <button
                    class="mr-8 text-gray-500 hover:text-gray-900"
                    :class="{ 'text-gray-900': activeTool === 'arrow' }"
                    @click="setImageEditorMode('arrow')"
                  >
                    <svg
                      class="fill-current w-5 h-5"
                      xmlns="http://www.w3.org/2000/svg"
                      viewBox="0 0 128 128"
                      xml:space="preserve"
                    >
                      <path
                        d="M62.398,76.681l22.341,47.476l-8.169,3.836L53.461,78.847l-28.66,33.711
		c0-8.981,0-112.552,0-112.552l78.398,77.441L62.398,76.681z"
                      />
                    </svg>
                  </button>

                  <!-- Undo Button -->
                  <button class="mr-2 text-gray-900" @click="editor.undo()">
                    <svg
                      class="fill-current w-8 h-8"
                      focusable="false"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M10.4,9.4c-1.7,0.3-3.2,0.9-4.6,2L3,8.5v7h7l-2.7-2.7c3.7-2.6,8.8-1.8,11.5,1.9c0.2,0.3,0.4,0.5,0.5,0.8l1.8-0.9  C18.9,10.8,14.7,8.7,10.4,9.4z"
                      ></path>
                    </svg>
                  </button>

                  <!-- Redo Button -->
                  <button class="mr-2 text-gray-900" @click="editor.redo()">
                    <svg
                      class="fill-current w-8 h-8"
                      focusable="false"
                      viewBox="0 0 24 24"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        d="M13.6,9.4c1.7,0.3,3.2,0.9,4.6,2L21,8.5v7h-7l2.7-2.7C13,10.1,7.9,11,5.3,14.7c-0.2,0.3-0.4,0.5-0.5,0.8L3,14.6  C5.1,10.8,9.3,8.7,13.6,9.4z"
                      ></path>
                    </svg>
                  </button>

                  <!-- Clear Button -->
                  <button class="text-gray-900" @click="editor.clear()">
                    <svg
                      class="w-6 h-6 fill-current"
                      viewBox="0 0 20 20"
                      xmlns="http://www.w3.org/2000/svg"
                    >
                      <path
                        fill-rule="evenodd"
                        clip-rule="evenodd"
                        d="M9 2C8.62123 2 8.27497 2.214 8.10557 2.55279L7.38197 4H4C3.44772 4 3 4.44772 3 5C3 5.55228 3.44772 6 4 6L4 16C4 17.1046 4.89543 18 6 18H14C15.1046 18 16 17.1046 16 16V6C16.5523 6 17 5.55228 17 5C17 4.44772 16.5523 4 16 4H12.618L11.8944 2.55279C11.725 2.214 11.3788 2 11 2H9ZM7 8C7 7.44772 7.44772 7 8 7C8.55228 7 9 7.44772 9 8V14C9 14.5523 8.55228 15 8 15C7.44772 15 7 14.5523 7 14V8ZM12 7C11.4477 7 11 7.44772 11 8V14C11 14.5523 11.4477 15 12 15C12.5523 15 13 14.5523 13 14V8C13 7.44772 12.5523 7 12 7Z"
                        fill="#4A5568"
                      />
                    </svg>
                  </button>
                </div>
              </div>
              <!-- Bottom Toolbar Row -->
              <div class="mb-2">
                <!-- Pen Stroke Widths -->
                <div class="flex flex-no-wrap justify-between items-center">
                  <div class="flex items-center">
                    <span class="mr-3">Pencil Width: </span>
                    <button
                      class="mr-4 w-5 h-5 rounded-full hover:bg-black focus:shadow-outline"
                      :class="
                        params.strokeWidth === 7
                          ? 'bg-black shadow-outline'
                          : 'bg-gray-500'
                      "
                      @click="updateStroke(7)"
                    ></button>
                    <button
                      class="mr-4 w-4 h-4 rounded-full hover:bg-black focus:shadow-outline"
                      :class="
                        params.strokeWidth === 5
                          ? 'bg-black shadow-outline'
                          : 'bg-gray-500'
                      "
                      @click="updateStroke(5)"
                    ></button>
                    <button
                      class="mr-4 w-3 h-3 rounded-full hover:bg-black focus:shadow-outline"
                      :class="
                        params.strokeWidth === 3
                          ? 'bg-black shadow-outline'
                          : 'bg-gray-500'
                      "
                      @click="updateStroke(3)"
                    ></button>
                    <button
                      class="mr-4 rounded-full hover:bg-black hover:shadow-outline"
                      style="width: 8px; height: 8px;"
                      :class="
                        params.strokeWidth === 1
                          ? 'bg-black shadow-outline'
                          : 'bg-gray-500'
                      "
                      @click="updateStroke(1)"
                    ></button>
                    <button
                      class="btn btn-sm btn-gray"
                      @click="showImageManager()"
                    >
                      Image Library
                    </button>
                  </div>
                  <div class="flex justify-end items-center">
                    <div v-if="uploading">
                      <span class="text-green-700">{{ uploadProgress }} %</span>
                    </div>
                    <button
                      class="btn btn-sm btn-black"
                      @click="addImageToNote"
                    >
                      Save
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <button class="absolute top-0 right-0" @click="close">
              <icon name="close" class="h-8 w-8 fill-current text-gray-800" />
            </button>
          </div>
          <div class="flex">
            <ImageEditor
              :canvasWidth="imageEditorWidth"
              :canvasHeight="imageEditorWidth * 0.66"
              ref="editor"
              class="border border-gray-400 shadow bg-white"
              @hook:mounted="initialize"
            />
          </div>
        </div>

        <!-- Image Manager Modal-->
        <image-manager v-on:use-image="useBackgroundImage" :images="images" />
      </div>
    </slide-up-fade-transition>
  </on-click-outside>
</template>

<script>
  import Icon from './Icon'
  import ImageEditor from 'vue-image-markup'
  import ImageManager from './ImageManager'
  import OnClickOutside from './OnClickOutside'
  import Vapor from 'laravel-vapor'
  import SlideUpFadeTransition from '../ProgressNotes/Appointments/SlideUpFadeTransition'

  export default {
    name: 'QnImageEditor',
    components: {
      SlideUpFadeTransition,
      OnClickOutside,
      Icon,
      ImageEditor,
      ImageManager,
    },
    props: {
      show: {
        type: Boolean,
      },
      images: {
        type: Array,
        default: [],
      },
    },
    data() {
      return {
        imageEditorWidth: 10,
        showFileUploader: false,
        editorKey: 0,
        activeTool: 'freeDrawing',
        uploading: false,
        uploadProgress: 0,
        params: {
          fill: 'black',
          stroke: 'black',
          strokeWidth: 5,
        },
      }
    },
    watch: {
      params(params) {
        this.$nextTick(() => {
          this.editor.set(this.editor.currentActiveTool, params)
        })
      },
    },
    mounted() {
      window.onresize = () => {
        this.resizeImageEditor()
      }
    },
    computed: {
      editor() {
        return this.$refs.editor
      },
    },
    methods: {
      calculateImageEditorWidth() {
        let parentWidth = this.$parent.$el.clientWidth - 40
        return parentWidth < 1115 ? parentWidth : 1115
      },
      resizeImageEditor() {
        this.imageEditorWidth = this.calculateImageEditorWidth()

        this.editor.canvas.setDimensions({
          width: this.imageEditorWidth,
          height: this.imageEditorWidth * 0.66,
        })
      },
      initialize() {
        this.$nextTick(() => {
          this.resizeImageEditor()
          this.editor.set('freeDrawing')
        })
      },
      setImageEditorMode(mode) {
        this.activeTool = mode
        this.editor.set(mode, this.params)
      },
      updateColors(event) {
        console.log(event.target.value)
        let newColor = event.target.value

        this.params = {
          ...this.params,
          ...{ fill: newColor, stroke: newColor },
        }
      },
      updateStroke(strokeWidth) {
        this.params = { ...this.params, ...{ strokeWidth: strokeWidth } }
      },
      useBackgroundImage(image) {
        this.editor.clear()
        this.editor.setBackgroundImage(image.signed_download_url)
      },
      showImageManager() {
        this.imageEditorWidth = this.calculateImageEditorWidth()
        this.$modal.show('image-manager')
      },
      addImageToNote() {
        this.uploading = true
        let base64Image = this.editor.saveImage()
        let file = this.dataURLtoFile(base64Image, 'note-image.png')

        // Store the file in /tmp on s3.
        Vapor.store(file, {
          progress: (progress) => {
            this.uploadProgress = Math.round(progress * 100)
          },
        }).then((response) => {
          this.$emit('note-image-added', {
            key: response.key,
            bucket: response.bucket,
            content_type: file.type,
          })

          this.editor.clear()
          this.close()
        })
      },
      dataURLtoFile(dataurl, filename) {
        var arr = dataurl.split(','),
          mime = arr[0].match(/:(.*?);/)[1],
          bstr = atob(arr[1]),
          n = bstr.length,
          u8arr = new Uint8Array(n)

        while (n--) {
          u8arr[n] = bstr.charCodeAt(n)
        }

        return new File([u8arr], filename, { type: mime })
      },
      close() {
        this.uploading = false
        this.uploadProgress = 0
        this.$emit('update:showImageEditor')
      },
    },
  }
</script>

<style scoped></style>
