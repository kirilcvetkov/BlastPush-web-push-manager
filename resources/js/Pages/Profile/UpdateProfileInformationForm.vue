<template>
  <jet-form-section @submitted="updateProfileInformation">
    <template #title>
      Profile Information
    </template>

    <template #description>
      Update your account's profile information and email address.
    </template>

    <template #form>
      <div class="form-group row" v-if="$page.jetstream.managesProfilePhotos">
        <label for="photo" class="col-md-3 col-form-label"> </label>
        <div class="col-md-9 row col">
          <div class="col-md-4">
            <div v-show="! photoPreview">
              <img id="photo" :src="$page.user.profile_photo_url" alt="Current Profile Photo"
                class="img-fluid rounded-circle d-block" style="max-height: 150px;">
            </div>
            <div v-show="photoPreview">
              <span class="img-fluid rounded-circle d-block" style="max-width: 150px; height: 150px;"
                :style="'background-size: cover; background-repeat: no-repeat; background-position: center center; background-image: url(\'' + photoPreview + '\');'">
              </span>
            </div>
          </div>

          <div class="col-md-8 d-flex align-items-center">
            <input type="file" class="file-upload-default" ref="photo" @change="updatePhotoPreview" accept="image/*">

            <jet-button color="btn-gradient-light" icon="mdi-upload" type="button" @click.native.prevent="selectNewPhoto">
              Select A New Photo
            </jet-button>

            <jet-button color="btn-gradient-light" icon="mdi-delete" type="button" @click.native.prevent="deletePhoto" v-if="user.profile_photo_path">
              Remove Photo
            </jet-button>
          </div>

          <div class="offset-md-4 col-md-8">
            <jet-input-error :message="form.error('photo')" class="mt-2" />
          </div>
        </div>
      </div>

      <jet-input id="first_name" type="text" label="First Name" :error="form.error('first_name')" v-model="form.first_name" autofocus />
      <jet-input id="last_name" type="text" label="Last Name" :error="form.error('last_name')" v-model="form.last_name" />
      <jet-input id="email" type="email" label="Email" :error="form.error('email')" v-model="form.email" />
      <jet-input id="phone" type="tel" label="Phone" :error="form.error('phone')" v-model="form.phone" />
      <jet-input id="company" type="text" label="Company" :error="form.error('company')" v-model="form.company" />
      <jet-input id="website" type="text" label="Website" :error="form.error('website')" v-model="form.website" />

      <div class="form-group row">
        <label for="country" class="col-sm-3 col-form-label">Country</label>
        <div class="col-sm-9">
          <select id="country" name="country" class="form-control text-dark" v-model="form.country">
            <option v-for="country in $page.countries" :value="country.iso">
              {{ country.nicename }}
            </option>
          </select>
        </div>
      </div>
    </template>

    <template #actions>
      <jet-button :disabled="form.processing">
        Save
      </jet-button>

      <jet-action-message :on="form.recentlySuccessful">
        Saved.
      </jet-action-message>
    </template>
  </jet-form-section>
</template>

<script>
  import JetFormSection from './../../Jetstream/FormSection'
  import JetInputError from './../../Jetstream/InputError'
  import JetActionMessage from './../../Jetstream/ActionMessage'
  import JetInput from '../../Jetstream/Input'
  import JetButton from './../../Jetstream/Button'

  export default {
    components: {
      JetFormSection,
      JetInputError,
      JetActionMessage,
      JetInput,
      JetButton,
    },

    props: ['user'],

    data() {
      return {
        form: this.$inertia.form({
          '_method': 'PUT',
          first_name: this.user.first_name,
          last_name: this.user.last_name,
          email: this.user.email,
          phone: this.user.phone,
          country: this.user.country,
          website: this.user.website,
          company: this.user.company,
          photo: null,
        }, {
          bag: 'updateProfileInformation',
          resetOnSuccess: false,
        }),

        photoPreview: null,
      }
    },

    methods: {
      updateProfileInformation() {
        if (this.$refs.photo) {
          this.form.photo = this.$refs.photo.files[0]
        }

        this.form.post(route('user-profile-information.update'), {
          preserveScroll: true
        });
      },

      selectNewPhoto() {
        this.$refs.photo.click();
      },

      updatePhotoPreview() {
        const reader = new FileReader();

        reader.onload = (e) => {
          this.photoPreview = e.target.result;
        };

        reader.readAsDataURL(this.$refs.photo.files[0]);
      },

      deletePhoto() {
        this.$inertia.delete(route('current-user-photo.destroy'), {
          preserveScroll: true,
        }).then(() => {
          this.photoPreview = null
        });
      },
    },
  }
</script>
