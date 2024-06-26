<template>
  <div>
    <h1 class="mb-8 font-bold text-3xl">
      <inertia-link
        class="text-blue-600 hover:text-blue-800"
        :href="route('clients.show', client.id)"
        >{{ client.full_name }}
      </inertia-link>
      <span class="text-blue-500 font-medium">/</span> Documents
    </h1>
    <!-- Download section for pdfs. -->
    <div v-if="isPdf" class="max-w-6xl">
      <div class="flex justify-between items-end mb-8">
        <p>{{ document.name }}</p>
        <a class="btn btn-black" :href="document.signed_download_url">
          Download
        </a>
      </div>
      <div class="flex justify-center">
        <iframe
          :src="document.signed_url"
          class="w-full h-screen pb-12"
          frameborder="0"
        ></iframe>
      </div>
    </div>

    <!-- printing section for text and images. -->
    <div
      id="qn-print-section"
      class="fr-view bg-white border border-gray-100 shadow-lg p-4"
      v-else
    >
      <div class="px-10">
        <div v-if="!isPdf" class="flex justify-between mb-8">
          <div>
            <p class="font-bold text-lg mb-2">
              {{ client.full_name }}
            </p>
            <p><span class="font-bold">DOB:</span> {{ client.birth_date }}</p>
            <p>
              <span class="font-bold">DOS:</span>
              {{ document.created_at }}
            </p>
          </div>
          <div>
            <img
              class="h-12 w-auto"
              :src="$page.logos.quickernotes_logo"
              alt="quickernotes"
            />
          </div>
        </div>
        <!-- Display the document's content.-->
        <div class="border-t pt-10">
          <div v-if="document.type === 'text'" v-html="document.content"></div>
          <div v-if="document.type === 'file'">
            <div v-if="isImage">
              <img :src="document.signed_url" :alt="document.name" />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="flex justify-end mt-6">
      <button class="btn btn-white mr-4">edit</button>
      <button class="btn btn-black" @click="print">
        Print
      </button>
    </div>
  </div>
</template>

<script>
  import Layout from '../Shared/Layout'

  export default {
    metaInfo: { title: 'Documents' },
    layout: (h, page) => h(Layout, [page]),
    props: {
      document: Object,
    },
    computed: {
      client() {
        return this.document.client
      },
      isImage() {
        const imageContentTypes = ['image/jpeg', 'image/png', 'image/jpg']
        return imageContentTypes.includes(this.document.content_type)
      },

      isPdf() {
        const fileTypes = ['application/pdf']
        return fileTypes.includes(this.document.content_type)
      },
    },

    methods: {
      print() {
        window.print()
      },
    },
  }
</script>

<style>
  @media print {
    body * {
      visibility: hidden;
    }

    #qn-print-section,
    #qn-print-section * {
      visibility: visible;
    }

    #qn-print-section > table,
    img,
    svg {
      break-inside: avoid;
    }

    #qn-print-section {
      width: 960px;
      position: absolute;
      left: 0;
      top: 0;
      box-shadow: none;
      border: none;
    }
  }
</style>
