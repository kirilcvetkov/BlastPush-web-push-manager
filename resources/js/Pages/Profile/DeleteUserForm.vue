<template>
  <jet-action-section>
    <template #title>
      Delete Account
    </template>

    <template #description>
      Permanently delete your account.
    </template>

    <template #content>
      <p>
        Once your account is deleted, all of its resources and data will be permanently deleted.
        Before deleting your account, please download any data or information that you wish to retain.
      </p>

      <div class="mt-4">
        <jet-button @click.native="confirmUserDeletion" color="btn-gradient-danger" icon="mdi-delete-forever">
          Delete Account
        </jet-button>
      </div>

      <!-- Delete Account Confirmation Modal -->
      <jet-dialog-modal :show="confirmingUserDeletion" @close="confirmingUserDeletion = false">
        <template #title>
          Delete Account
        </template>

        <template #content>
          <p>Are you sure you want to delete your account? Once your account is deleted,
          all of its resources and data will be permanently deleted.
          Please enter your password to confirm you would like to permanently delete your account.</p>

          <jet-input id="password" type="password" label="Password" class="mt-4" ref="password" v-model="form.password"
            @keyup.enter.native="deleteUser" :error="form.error('password')"/>
        </template>

        <template #footer>
          <jet-secondary-button @click.native="confirmingUserDeletion = false">
            Nevermind
          </jet-secondary-button>

          <jet-button class="ml-2" @click.native="deleteUser" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" color="btn-gradient-danger" icon="mdi-delete-forever">
            Delete Account
          </jet-button>
        </template>
      </jet-dialog-modal>
    </template>
  </jet-action-section>
</template>

<script>
  import JetActionSection from './../../Jetstream/ActionSection'
  import JetButton from './../../Jetstream/Button'
  import JetDialogModal from './../../Jetstream/DialogModal'
  import JetDangerButton from './../../Jetstream/DangerButton'
  import JetInput from './../../Jetstream/Input'
  import JetInputError from './../../Jetstream/InputError'
  import JetSecondaryButton from './../../Jetstream/SecondaryButton'

  export default {
    components: {
      JetActionSection,
      JetButton,
      JetDangerButton,
      JetDialogModal,
      JetInput,
      JetInputError,
      JetSecondaryButton,
    },

    data() {
      return {
        confirmingUserDeletion: false,
        deleting: false,

        form: this.$inertia.form({
          '_method': 'DELETE',
          password: '',
        }, {
          bag: 'deleteUser'
        })
      }
    },

    methods: {
      confirmUserDeletion() {
        this.form.password = '';

        this.confirmingUserDeletion = true;

        setTimeout(() => {
          this.$refs.password.focus()
        }, 250)
      },

      deleteUser() {
        this.form.post(route('current-user.destroy'), {
          preserveScroll: true
        }).then(response => {
          if (! this.form.hasErrors()) {
            this.confirmingUserDeletion = false;
          }
        })
      },
    },
  }
</script>
