<template>
  <div>
    <!-- Template Editor -->
    <div class="mb-6">
      <text-input
        type="text"
        name="name"
        label="Name"
        required
        :errors="$page.errors.name"
        help-text="The name of the template"
        v-model="form.name"
      />
    </div>
    <div class="flex flex-wrap mb-6">
      <text-input
        type="text"
        name="default_short_name"
        label="Short Name"
        class="flex-1 mr-2"
        :errors="$page.errors.default_short_name"
        help-text="A short name, visible on buttons and menu's (optional)"
        v-model="form.default_short_name"
      />
      <text-input
        type="text"
        name="default_group_name"
        label="Group Name"
        class="flex-1"
        :errors="$page.errors.default_group_name"
        help-text="A group name so templates can be organized into groups (optional)"
        v-model="form.default_group_name"
      />
    </div>
    <div class="bg-white rounded-lg shadow-lg p-3 flex flex-wrap">
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:first_name}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:last_name}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:full_name}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:age}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:dob}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:gender}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:him_her}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:he_she}</span
      >
      <span class="tag-button border rounded px-2 py-1 mr-4 mt-2"
        >{$client:his_her}</span
      >
      <span class="tag-button border rounded px-2 py-1 mt-2">
        {$appointment:service_name}
      </span>
    </div>
    <froala
      class=""
      ref="editor"
      id="edit"
      :contenteditable="false"
      :tag="'textarea'"
      :config="config"
      v-model="form.content"
    />
    <div class="flex justify-end items-end mt-2">
      <inertia-link
        :href="route('templates.index')"
        class="text-gray-700 underline hover:text-gray-800 mr-3"
      >
        cancel
      </inertia-link>
      <button
        class="btn btn-black"
        @click.prevent="save"
        :disabled="!validForm"
      >
        Save
      </button>
    </div>
  </div>
</template>
<script>
  import TextInput from '../Shared/TextInput'

  export default {
    name: 'template-editor',
    components: { TextInput },
    props: {
      form: {},
      save: {},
    },
    data() {
      return {
        config: {
          key:
            'WE1B5dF3C3I3C9C7C7cWHNGGDTCWHIg1Ee1Oc2Yc1b1Lg1POkB6B5F5B4F3E3G3F2B6A2==',
          attribution: false,
          placeHolder: 'Enter your template...',
          events: {
            initialized: () => {
              let editor = this.$refs.editor.getEditor()
              editor.events.bindClick(
                editor.$('body'),
                'span.tag-button',
                function (event) {
                  editor.html.insert(event.target.innerText)
                  editor.events.focus()
                },
              )
            },
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
    computed: {
      validForm() {
        return this.form.content && this.form.name
      },
    },
  }
</script>

<style scoped>
  .tag-button {
    @apply bg-gray-300 cursor-pointer shadow py-2;
  }

  .tag-button:hover {
    @apply bg-gray-700 text-white;
  }
</style>
