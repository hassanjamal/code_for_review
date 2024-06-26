<template>
  <div class="container mx-auto mt-20">
    <div class="bg-white rounded shadow overflow-hidden w-full p-20">
      <vue-form-generator ref="vfg" :schema="getSchema" :model="model" />
    </div>
  </div>
</template>

<script>
  import 'vue-form-generator/dist/vfg.css'

  export default {
    metaInfo: { title: 'Forms' },
    props: ['formTemplate', 'code'],
    data() {
      return {
        model: {},
        formSubmit: false,
      }
    },
    computed: {
      getSchema() {
        // Add the submit button.
        this.formTemplate.groups.push({
          legend: null,
          fields: [
            {
              type: 'submit',
              buttonText: 'Submit Form',
              styleClasses: 'col-12 ',
              validateBeforeSubmit: true,
              onSubmit: () => {
                this.submit()
              },
            },
          ],
        })

        return this.formTemplate
      },
    },
    mounted() {
      this.$root.$on('signatureGenerated', (value) => {
        this.$inertia.post(route('intake-forms.store'), {
          code: this.code,
          model: this.model,
          signature: value,
        })
      })
    },
    methods: {
      clear() {
        this.$refs.signaturePad.clearSignature()
      },
      submit() {
        this.$root.$emit('clickedSubmitButton')
      },
    },
  }
</script>

<style>
  #signature {
    border: double 3px transparent;
    border-radius: 5px;
    background-image: linear-gradient(white, white),
      radial-gradient(circle at top left, #4bc5e8, #9f6274);
    background-origin: border-box;
    background-clip: content-box, border-box;
  }
</style>
