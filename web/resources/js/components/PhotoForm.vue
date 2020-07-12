<template>
  <div v-show="value" class="photo-form">
    <h2 class="title">Submit a photo</h2>
    <div v-show="loading" class="panel">
      <Loader>Sending your photo...</Loader>
    </div>
    <form v-show="! loading" class="form" @submit.prevent="submit">
      <div class="errors" v-if="errors">
        <ul v-if="errors.photo">
          <li v-for="msg in errors.photo" :key="msg">{{ msg }}</li>
        </ul>
      </div>
      <input class="form__item" type="file" @change="onFileChange">
      <output class="form__output" v-if="preview">
        <img :src="preview" alt="">
      </output>
      <div class="form__button">
        <button type="submit" class="button button--inverse">submit</button>
      </div>
    </form>
  </div>
</template>

<script>
import { CREATED, UNPROCESSABLE_ENTITY } from '../util'
import Loader from './Loader.vue'

export default {
  components: {
    Loader
  },
  props: {
    value: {
      type: Boolean,
      required: true,
    }
  },
  data () {
    return {
      loading: false,
      preview: null,
      photo: null,
      errors: null
    }
  },
  methods: {
    onFileChange (event) {
      // Processing is interrupted if nothing is selected
      if (event.target.files.length === 0) {
        this.reset()
        return false
      }

      // If the file is not an image, stop processing
      if (! event.target.files[0].type.match('image.*')) {
        this.reset()
        return false
      }

      // Get an instance of the FileReader class
      const reader = new FileReader()

      // Processing to be executed when the file has been read
      reader.onload = e => {
        // Substitute the read result (data URL) in preview
        // If a value is entered in preview, the v-if attached to <output> is determined to be true
        // Also, the src attribute of <img> inside <output> refers to the value of preview.
        // The resulting image is displayed
        this.preview = e.target.result
      }

      // Read a file
      // The read file can be received in the data URL format (see onload above)
      reader.readAsDataURL(event.target.files[0])
      this.photo = event.target.files[0]
    },
    // Method to clear the input field value and preview display
    reset () {
      this.preview = ''
      this.photo = null
      this.$el.querySelector('input[type="file"]').value = null
    },
    async submit () {
      this.loading = true

      const formData = new FormData()
      formData.append('photo', this.photo)
      const response = await axios.post('/api/photos', formData)

      this.loading = false

      if (response.status === UNPROCESSABLE_ENTITY) {
        this.errors = response.data.errors
        return false
      }

      this.reset()
      this.$emit('input', false)

      if (response.status !== CREATED) {
        this.$store.commit('error/setCode', response.status)
        return false
      }

      // Message registration
      this.$store.commit('message/setContent', {
        content: 'Upload photo success！',
        timeout: 6000
      })

      this.$router.push(`/photos/${response.data.id}`)
    }
  }
}
</script>
