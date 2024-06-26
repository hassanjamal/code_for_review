<template>
  <div class="col-12">
    <vue-signature-pad
      id="signature"
      width="50%"
      height="180px"
      saveType="image/svg+xml"
      ref="signaturePad"
      :options="signatureOptions"
    />
    <span class="text-red-400 text-sm cursor-pointer" @click="clear"
      >clear</span
    >
  </div>
</template>

<script>
  import { abstractField } from 'vue-form-generator'

  export default {
    mixins: [abstractField],
    name: 'fieldSignaturePad',
    data() {
      return {
        signatureOptions: {
          // penColor: "#000"
        },
      }
    },
    methods: {
      clear() {
        this.$refs.signaturePad.clearSignature()
      },
    },
    mounted() {
      this.$root.$on('clickedSubmitButton', () => {
        const { isEmpty, data } = this.$refs.signaturePad.saveSignature()
        if (!isEmpty) {
          this.$root.$emit('signatureGenerated', data)
        } else {
          alert(' You need to provide your signature ')
        }
      })
    },
  }
</script>

<style scoped></style>
