<template>
  <div>
    <label v-if="label" class="form-label" :for="id">
      {{ label }}:
      <span v-if="isRequired" class="text-sm text-red-800">*</span>
    </label>
    <input
      :id="id"
      ref="input"
      v-bind="$attrs"
      class="form-input"
      :class="{ error: errors.length }"
      :type="type"
      :value="value"
      @input="$emit('input', $event.target.value)"
    />
    <p
      v-if="helpText"
      class="text-gray-600 text-sm italic ml-2 mt-1"
      v-text="helpText"
    ></p>
    <div v-if="errors.length" class="form-error">{{ errors[0] }}</div>
  </div>
</template>

<script>
  export default {
    inheritAttrs: false,
    props: {
      id: {
        type: String,
        default() {
          return `text-input-${this._uid}`
        },
      },
      type: {
        type: String,
        default: 'text',
      },
      value: String,
      label: String,
      errors: {
        type: Array,
        default: () => [],
      },
      helpText: {
        type: String,
      },
    },
    computed: {
      isRequired() {
        return this.$attrs.hasOwnProperty('required')
      },
    },
    methods: {
      focus() {
        this.$refs.input.focus()
      },
      select() {
        this.$refs.input.select()
      },
      setSelectionRange(start, end) {
        this.$refs.input.setSelectionRange(start, end)
      },
    },
  }
</script>
