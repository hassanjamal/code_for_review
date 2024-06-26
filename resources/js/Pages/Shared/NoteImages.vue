<template>
  <div class="container mx-auto text-center w-full">
    <!--Pen Color-->
    <div class="flex justify-between items-center my-4">
      <span class="text-center uppercase">Pen Color </span>
      <span
        class="p-2 border-2 border-gray-900 bg-gray-900 hover:bg-gray rounded-full"
        :class="{ active: penColor === 'black' }"
        @click="penColor = 'black'"
      ></span>
      <span
        class="p-2 border-2 border-green bg-green-600 hover:bg-green-700 rounded-full"
        :class="{ active: penColor === 'green' }"
        @click="penColor = 'green'"
      ></span>
      <span
        class="p-2 border-2 border-blue bg-blue-600 hover:bg-blue-700 rounded-full"
        :class="{ active: penColor === 'blue' }"
        @click="penColor = 'blue'"
      ></span>
      <span
        class="p-2 border-2 border-red bg-red-600 hover:bg-red-700 rounded-full"
        :class="{ active: penColor === 'red' }"
        @click="penColor = 'red'"
      ></span>
      <span
        class="p-2 border-2 border-yellow bg-yellow-600 hover:bg-yellow-700 rounded-full"
        :class="{ active: penColor === 'yellow' }"
        @click="penColor = 'yellow'"
      ></span>
      <span
        class="p-2 border-2 border-orange bg-orange-600 hover:bg-orange-700 rounded-full"
        :class="{ active: penColor === 'orange' }"
        @click="penColor = 'orange'"
      ></span>
    </div>
    <!--Pen Width-->
    <div class="flex justify-between items-center my-4">
      <span class="text-center uppercase">Pen Width </span>
      <form class="flex justify-between items-center">
        <div class="mr-4">
          <input type="radio" v-model="penWidth" name="penWidth" value="1" />
          Thin
        </div>
        <div class="mr-4">
          <input
            class="mr-4"
            type="radio"
            v-model="penWidth"
            name="penWidth"
            value="5"
          />
          Medium
        </div>
        <div>
          <input type="radio" v-model="penWidth" name="penWidth" value="10" />
          Wide
        </div>
      </form>
    </div>
    <hr />
    <div v-if="hasBackgrounds" class="flex-col my-4">
      <div class="flex justify-start items-baseline mb-2 flex-wrap">
        <img
          v-for="diagram in bgDiagrams"
          :key="diagram.id"
          :id="'bg' + diagram.id"
          ref="bg{{diagram.id}}"
          crossorigin="anonymous"
          class="h-16 w-16 shadow border border-gray-800 rounded-lg mb-3 mr-2"
          :src="diagram.url"
          @click="setBg(diagram.id)"
        />
        <a
          href="/templates"
          class="mr-2 no-underline hover:no-underline"
          v-tooltip="'new background'"
        >
          <span class="fa fa-plus-square-o fa-2x text-blue-700"></span>
        </a>
      </div>
    </div>

    <div v-show="hasBackgrounds" ref="canvasDiv">
      <canvas
        v-show="canvasReady"
        ref="canvas"
        id="body-diagram"
        class="border border-gray-300 cursor-pointer overflow-scroll"
        @mousedown="handleMouseDown"
        @mousemove="handleMouseMove"
        @mouseup="handleMouseUp"
        @mouseleave="handleMouseUp"
      ></canvas>
    </div>

    <div
      v-show="!hasBackgrounds"
      class="p-8 border border-gray-200 shadow rounded mb-8"
    >
      <p>
        You need to upload at least one background before you can add diagrams
        to notes.
      </p>
      <a href="/templates" class="self-start">add background</a>
    </div>

    <div class="flex justify-between items-end">
      <div>
        <a
          class="mr-4 text-gray-700 hover:text-gray-900 cursor-pointer"
          @click="$emit('drawing-closed')"
        >
          close
        </a>
        <a
          v-show="canSave"
          class="text-blue-700 hover:text-blue-700 cursor-pointer"
          @click.prevent="clearCanvas"
        >
          clear
        </a>
      </div>

      <div>
        <button
          class="py-3 px-5 bg-blue-600 text-white font-bold hover:bg-blue-700 uppercase rounded"
          :disabled="!canSave"
          @click.prevent="save"
        >
          Attach Diagram To Note
        </button>
      </div>
    </div>
  </div>
</template>

<script>
  const axios = window.axios

  export default {
    name: 'NoteImages',

    data() {
      return {
        canvasReady: false,
        canvas: null,
        ctx: null,
        paint: false,
        clickX: [],
        clickY: [],
        clickDrag: [],
        clickColor: [],
        clickPenWidth: [],
        penColor: 'black',
        penWidth: 5,
        bgDiagrams: [
          {
            id: 1,
            url:
              'https://qn2020-local.s3-us-west-1.amazonaws.com/tenant_656d0b65-6688-4c03-b87d-2bf46f886050/Syog70Do4FOWKZ1WSd7c2qLeXPF1n8JVluVPwpJ2.jpeg',
          },
        ],
        selectedBg: null,
      }
    },

    mounted() {
      this.canvas = this.$refs.canvas

      this.addTouchEvents()
    },

    computed: {
      hasBackgrounds() {
        return this.bgDiagrams.length
      },

      offsetX() {
        return this.canvas.getBoundingClientRect().left
      },

      offsetY() {
        return this.canvas.getBoundingClientRect().top + window.scrollY
      },

      canSave() {
        return this.clickX.length > 0
      },

      activeColorHex() {
        const colors = {
          red: '#ff0003',
          black: '#141414',
          orange: '#ff7902',
          yellow: '#fff600',
          blue: '#0010ff',
          green: '#02ff00',
        }

        return colors[this.penColor]
      },
    },

    methods: {
      setBg(id) {
        this.clearCanvas()

        this.ctx = this.canvas.getContext('2d')

        this.selectedBg = this.bgDiagrams.find((bg) => {
          return bg.id === id
        })

        this.canvasReady = true

        this.redraw()
      },

      save() {
        let noteDiagram = this.canvas.toDataURL('image/png')

        this.$emit('drawing-saved', { noteDiagram })

        this.clearCanvas()
      },

      handleMouseDown(e) {
        let mouseX = e.pageX - this.offsetX
        let mouseY = e.pageY - this.offsetY

        this.paint = true

        this.addClick(mouseX, mouseY)

        this.redraw()
      },

      handleMouseMove(e) {
        if (this.paint) {
          this.addClick(e.pageX - this.offsetX, e.pageY - this.offsetY, true)

          this.redraw()
        }
      },

      handleMouseUp() {
        this.paint = false
      },

      addClick(x, y, dragging = false) {
        this.clickX.push(x)
        this.clickY.push(y)
        this.clickDrag.push(dragging)
        this.clickColor.push(this.activeColorHex)
        this.clickPenWidth.push(this.penWidth)
      },

      clearCanvas() {
        this.canvasReady = false
        this.clickX = []
        this.clickY = []
        this.clickDrag = []
        this.clickColor = []
        this.clickPenWidth = []
        this.penColor = 'black'
        this.selectedBg = null
      },

      initializeBg() {
        let bgImage = document.getElementById('bg' + this.selectedBg.id)

        this.$refs.canvas.width = bgImage.naturalWidth
        this.$refs.canvas.height = bgImage.naturalHeight
        this.ctx.clearRect(0, 0, bgImage.naturalWidth, bgImage.naturalHeight)
        this.ctx.drawImage(bgImage, 0, 0)
      },

      redraw() {
        this.initializeBg()

        this.ctx.lineJoin = 'round'

        for (let i = 0; i < this.clickX.length; i++) {
          this.ctx.beginPath()
          if (this.clickDrag[i] && i) {
            this.ctx.moveTo(this.clickX[i - 1], this.clickY[i - 1])
          } else {
            this.ctx.moveTo(this.clickX[i] - 1, this.clickY[i])
          }
          this.ctx.lineTo(this.clickX[i], this.clickY[i])
          this.ctx.closePath()
          this.ctx.strokeStyle = this.clickColor[i]
          this.ctx.lineWidth = this.clickPenWidth[i]
          this.ctx.stroke()
        }
      },

      addTouchEvents() {
        this.canvas.addEventListener(
          'touchstart',
          (e) => {
            e.preventDefault()

            let touch = e.touches[0]
            let mouseEvent = {
              pageX: touch.pageX,
              pageY: touch.pageY,
            }
            // this.canvas.dispatchEvent(mouseEvent)
            this.handleMouseDown(mouseEvent)
          },
          false,
        )

        this.canvas.addEventListener(
          'touchend',
          (e) => {
            e.preventDefault()

            this.handleMouseUp()
          },
          false,
        )

        this.canvas.addEventListener(
          'touchmove',
          (e) => {
            e.preventDefault()
            let touch = e.touches[0]
            let mouseEvent = {
              pageX: touch.pageX,
              pageY: touch.pageY,
            }

            this.handleMouseMove(mouseEvent)
          },
          false,
        )
      },
    },
  }
</script>

<style scoped>
  .active {
    border: 2px solid black;
  }
</style>
