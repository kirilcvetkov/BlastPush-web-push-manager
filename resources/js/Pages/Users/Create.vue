<template>
  <app-layout>
    <template #title>Message</template>
    <template #icon>
      <i class="mdi mdi-web mdi-18px"></i>
    </template>

    <div class="card">
      <div class="card-body">
        <form method="POST" action="" @submit.prevent="submit" enctype="multipart/form-data"
          @keydown="$page.errors = {}">

          <jet-input id="title" type="text" label="Title" :error="$page.errors.title" v-model="form.title" autofocus />
          <jet-form-line id="url" label="Link">
            <codemirror v-model="form.url" :options="cmOptions" class="border p-1"></codemirror>
          </jet-form-line>

          <jet-input id="body" type="text" label="Body" :error="$page.errors.body" v-model="form.body" />
          <jet-input id="button" type="text" label="Button Text" :error="$page.errors.button" v-model="form.button" />
          <jet-input-upload button="Upload" title="Icon" :src="form.icon" @upload="form.iconupload = $event" @delete="form.icon = null"></jet-input-upload>
          <jet-input-upload button="Upload" title="Image" :src="form.image" @upload="form.imageupload = $event" @delete="form.image = null"></jet-input-upload>
          <jet-input-upload button="Upload" title="Badge" :src="form.badge" @upload="form.badgeupload = $event" @delete="form.badge = null"></jet-input-upload>
          <jet-input-upload button="Upload" title="Sound" :src="form.sound" accept="audio/*" v-bind:isAudio="true" @upload="form.soundupload = $event" @delete="form.sound = null"></jet-input-upload>

          <jet-button type="submit" :disabled="form.processing">Save changes</jet-button>
          <inertia-link class="btn btn-gradient-light" :href="route('messages.index')">
            <i class="mdi mdi-close"></i> Cancel
          </inertia-link>
        </form>
      </div>
    </div>
  </app-layout>
</template>

<script>
  import AppLayout from '../../Layouts/AppLayout';
  import JetInput from '../../Jetstream/Input'
  import JetButton from './../../Jetstream/Button'
  import JetInputUpload from './../../Jetstream/InputUpload'
  import JetFormLine from './../../Jetstream/FormLine'
  import { codemirror } from 'vue-codemirror'
  import './../../../css/codemirror.css'
  import 'codemirror/mode/css/css.js'

  export default {
    components: {
      AppLayout,
      JetInput,
      JetButton,
      JetInputUpload,
      JetFormLine,
      codemirror
    },

    props: {
      id: { type: Number, default: null },
      title: { type: String, default: null },
      url: { type: String, default: null },
      body: { type: String, default: null },
      button: { type: String, default: null },
      icon: { type: String, default: null },
      image: { type: String, default: null },
      badge: { type: String, default: null },
      sound: { type: String, default: null },
    },

    data() {
      return {
        form: this.$inertia.form({
          '_method': 'POST',
          title: this.title || null,
          url: this.url || null,
          body: this.body || null,
          button: this.button || null,
          icon: this.icon || null,
          iconupload: null,
          image: this.image || null,
          imageupload: null,
          badge: this.badge || null,
          badgeupload: null,
          sound: this.sound || null,
          soundupload: null,
        }, {
          bag: 'messages',
          resetOnSuccess: false,
        }),
        cmOptions: {
          mode: 'text/css',
          lineWrapping: true,
        }
      }
    },

    methods: {
      submit() {
        if (this.id) {
          this.form._method = 'PUT';
          this.form.post(route('messages.update', this.id), { preserveScroll: true });
        } else {
          this.form.post(route('messages.store'), { preserveScroll: true });
        }
      },
    },
  }
</script>
<style scoped>
.CodeMirror {
  max-height: 100px !important;
}
</style>
