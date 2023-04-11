<template>
  <jet-form-section @submitted="updatePassword">
    <template #title>
      Update Password
    </template>

    <template #description>
      Ensure your account is using a long, random password to stay secure.
    </template>

    <template #form>
      <jet-input id="current_password" type="password" label="Current Password" v-model="form.current_password"
        ref="current_password" autocomplete="current-password" :error="form.error('current_password')" />

      <jet-input id="password" type="password" label="New Password" v-model="form.password" autocomplete="new-password"
        :error="form.error('password')"/>

      <jet-input id="password_confirmation" type="password" label="Confirm Password" v-model="form.password_confirmation"
        autocomplete="new-password" :error="form.error('password_confirmation')"/>
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
  import JetActionMessage from './../../Jetstream/ActionMessage'
  import JetButton from './../../Jetstream/Button'
  import JetFormSection from './../../Jetstream/FormSection'
  import JetInput from './../../Jetstream/Input'

  export default {
    components: {
      JetActionMessage,
      JetButton,
      JetFormSection,
      JetInput,
    },

    data() {
      return {
        form: this.$inertia.form({
          current_password: '',
          password: '',
          password_confirmation: '',
        }, {
          bag: 'updatePassword',
        }),
      }
    },

    methods: {
      updatePassword() {
        this.form.put(route('user-password.update'), {
          preserveScroll: true
        }).then(() => {
          this.$refs.current_password.focus()
        })
      },
    },
  }
</script>
