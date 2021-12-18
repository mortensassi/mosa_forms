import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
const path = require('path');


// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],

  build: {
    manifest: true,
    target: 'es2015'
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
