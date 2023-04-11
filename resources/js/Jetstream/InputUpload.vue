<template>
  <div class="form-group row">
    <label class="col-md-3 col-form-label">{{ title }}</label>
    <div class="col-md-9">
      <input type="file" :value="file" class="file-upload-default" ref="photo" @change="updatePhotoPreview" :accept="accept">

      <div class="input-group mb-3">
        <span class="input-group-prepend">
          <button class="btn btn-inverse-info" type="button" @click.prevent="selectNewPhoto">{{ button }}</button>
        </span>
        <input type="text" :value="src" class="form-control file-upload-info" :placeholder="title" readonly>
        <div class="input-group-append">
          <button class="btn btn-sm btn-inverse-danger" type="button" @click="deletePhoto">
            <i class="mdi mdi-18px mdi-delete-forever"></i>
          </button>
        </div>
      </div>
      <div v-show="! photoPreview" class="text-center">
        <figure v-if="isAudio" v-show="source">
          <audio controls :src="source">
            Your browser does not support the <code>audio</code> element.
          </audio>
        </figure>
        <img v-else :src="source" class="img-fluid rounded shadow-sm mx-auto d-block" style="max-height: 100px;">
      </div>
      <div v-show="photoPreview" class="text-center">
        <figure v-if="isAudio">
          <audio controls :src="photoPreview">
            Your browser does not support the <code>audio</code> element.
          </audio>
        </figure>
        <img v-else :src="photoPreview" class="img-fluid rounded shadow-sm mx-auto d-block" style="max-height: 100px;" />
      </div>
    </div>
  </div>
</template>

<script>
  export default {
    props: {
      button: { type: String, default: null },
      title: { type: String, default: null },
      src: { type: String, default: null },
      accept: { type: String, default: 'image/*' },
      isAudio: { type: Boolean, default: false },
    },


    data() {
      return {
        file: null,
        source: this.src,
        photoPreview: null,
      }
    },

    methods: {
      selectNewPhoto() {
        this.$refs.photo.click();
      },

      updatePhotoPreview(event) {
        const reader = new FileReader();

        reader.onload = (e) => {
          this.photoPreview = e.target.result;
          this.$emit('upload', this.$refs.photo.files[0]);
        };

        reader.readAsDataURL(this.$refs.photo.files[0]);
      },

      deletePhoto() {
        this.photoPreview = null;
        this.file = null;
        this.source = null;
        this.$emit('delete');
      },
    }
  }
</script>

