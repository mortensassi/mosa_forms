import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
const path = require('path')
import legacy from '@vitejs/plugin-legacy'


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [
    vue(),
  ],

  build: {
    manifest: true,
    lib: {
      name: 'sassi-forms',
      entry: './src/main.js'
    }
  },

  resolve: {
    alias: [
      {
        find: '@', replacement: path.resolve(__dirname, 'src'),
      },
      {
        find: '@styles', replacement: path.resolve(__dirname, 'src/assets/scss'),
      },
    ],
  },

  css: {
    preprocessorOptions: {
      scss: { additionalData: `@import "@/assets/scss/app";` },
    },
  },

  server: {
    host: true,
    https: true
  }
})
