<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-blue-600 hover:text-blue-800"
        :href="route('clients.show', client.id)"
        >{{ client.full_name }}
      </inertia-link>
      <span class="text-blue-500 font-medium">/</span> Add Document
    </h1>
    <div class="my-12">
      <span class="text-2xl mr-4"
        >Which type of document would you like to create?
      </span>
      <button class="btn btn-gray mr-4" @click.prevent="form.type = 'text'">
        Text
      </button>
      <button class="btn btn-gray" @click.prevent="form.type = 'file'">
        File
      </button> 
    </div>
    <div v-show="form.type" class="px-8">
      <form v-show="form.type === 'text'" @submit.prevent="submit" class="">
        <h2 class="text-xl mb-8">Text document</h2>
        <div class="mb-4">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            placeholder="Document name"
            label="Name"
          />
        </div>
        <div
          class="mb-4"
          :class="{
            'rounded-lg border border-red-500': $page.errors.content,
          }"
        >
          <froala
            class=""
            ref="editor"
            id="edit"
            :contenteditable="false"
            :tag="'textarea'"
            :config="config"
            v-model="form.content"
          />
        </div>
        <span class="text-red-500 text-sm" v-if="$page.errors.content">
          The content field is required.
        </span>
        <div class="flex justify-end">
          <loading-button
            :loading="sending"
            class="btn btn-black"
            type="submit"
          >
            Save
          </loading-button>
        </div>
      </form>
      <form
        v-show="form.type === 'file'"
        @submit.prevent="upload"
        class=""
        enctype="multipart/form-data"
      >
        <h2 class="text-xl mb-8">Upload a file</h2>
        <div class="mb-4">
          <text-input
            v-model="form.name"
            :errors="$page.errors.name"
            class=""
            placeholder="Document name"
            label="Name"
          />
        </div>
        <input type="file" name="upload" @change="onFileSelected" id="upload" />
        <div v-show="uploadProgress">
          <p>
            Upload Progress -
            <span class="text-green-600 italic">{{ uploadProgress }} %</span>
          </p>
        </div>
        <div class="flex justify-end">
          <loading-button
            :loading="sending"
            class="btn btn-black"
            type="submit"
          >
            Save
          </loading-button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
  import TextInput from '../Shared/TextInput'
  import LoadingButton from '../Shared/LoadingButton'
  import Layout from '../Shared/Layout'
  import Vapor from 'laravel-vapor'

  export default {
    metaInfo: { title: 'Create Document' },
    layout: (h, page) => h(Layout, [page]),
    props: ['client'],
    components: {
      TextInput,
      LoadingButton,
    },
    data() {
      return {
        sending: false,
        uploadProgress: 0,
        selectedFile: null,
        fileName: '',
        readyToUpload: false,
        form: {
          name: '',
          type: 'file',
          content: '',
        },
        config: {
          key:
            'WE1B5dF3C3I3C9C7C7cWHNGGDTCWHIg1Ee1Oc2Yc1b1Lg1POkB6B5F5B4F3E3G3F2B6A2==',
          attribution: false,
          events: {
            // initialized: () => {
            //   let editor = this.$refs.editor.getEditor()
            //   editor.events.bindClick(
            //     editor.$('body'),
            //     'span.tag-button',
            //     function(event) {
            //       editor.html.insert(event.target.innerText)
            //       editor.events.focus()
            //     },
            //   )
            // },
          },
          autofocus: true,
          pluginsEnabled: [
            'table',
            'quickInsert',
            'lists',
            'lineBreaker',
            'wordPaste',
            'entities',
            'help',
            'align',
            'fontSize',
          ],
          toolbarButtons: {
            // name for block of buttons
            moreText: {
              // buttons you need on this block
              buttons: [
                'bold',
                'italic',
                'underline',
                'strikeThrough',
                'fontSize',
                'textColor',
                'backgroundColor',
                'clearFormatting',
              ],
              align: 'left',
              buttonsVisible: 3,
            },
            moreParagraph: {
              buttons: [
                'alignLeft',
                'alignCenter',
                'formatOLSimple',
                'alignRight',
                'alignJustify',
                'formatUL',
                'paragraphFormat',
                'lineHeight',
                'outdent',
                'indent',
                'quote',
              ],
              align: 'left',
              buttonsVisible: 4,
            },
            moreRich: {
              buttons: ['insertTable'],
              align: 'left',
              buttonsVisible: 1,
            },
            moreMisc: {
              buttons: ['undo', 'redo'],
              align: 'right',
              buttonsVisible: 2,
            },
          },
          heightMin: 300,
          heightMax: 900,
        },
      }
    },
    methods: {
      onFileSelected(event) {
        this.readyToUpload = true
        this.fileName = event.target.files[0].name
        this.selectedFile = event.target.files[0]
      },
      submit() {
        this.sending = true

        this.$inertia
          .post(
            route('clients.text-documents.store', this.client.id),
            this.form,
          )
          .then((res) => {
            this.sending = false
          })
      },
      upload() {
        Vapor.store(this.selectedFile, {
          progress: (progress) => {
            this.uploadProgress = Math.round(progress * 100)
          },
        }).then((response) => {
          this.$inertia.post(
            route('clients.file-documents.store', this.client.id),
            {
              uuid: response.uuid,
              key: response.key,
              bucket: response.bucket,
              name: this.form.name,
              original_name: this.selectedFile.name,
              content_type: this.selectedFile.type,
            },
          )
        })
      },
    },
  }
</script>
