<template>
  <span>
    <span @click="startConfirmingPassword">
      <slot />
    </span>

    <jet-dialog-modal :show="confirmingPassword" @close="confirmingPassword = false">
      <template #title>
        {{ title }}
      </template>

      <template #content>
        {{ content }}

        <jet-input id="password" type="password" label="Password" class="mt-3" ref="password" v-model="form.password"
          @keyup.enter.native="confirmPassword" :error="form.error"/>
      </template>

      <template #footer>
        <jet-secondary-button @click.native="confirmingPassword = false">
          Nevermind
        </jet-secondary-button>

        <jet-button class="ml-2" @click.native="confirmPassword" :class="{ 'opacity-25': form.processing }" :disabled="form.processing" icon="mdi-check">
          {{ button }}
        </jet-button>
      </template>
    </jet-dialog-modal>
  </span>
</template>

<script>
  import JetButton from './Button'
  import JetDialogModal from './DialogModal'
  import JetInput from './Input'
  import JetInputError from './InputError'
  import JetSecondaryButton from './SecondaryButton'

  export default {
    props: {
      title: {
        default: 'Confirm Password',
      },
      content: {
        default: 'For your security, please confirm your password to continue.',
      },
      button: {
        default: 'Confirm',
      }
    },

    components: {
      JetButton,
      JetDialogModal,
      JetInput,
      JetInputError,
      JetSecondaryButton,
    },

    data() {
      return {
        confirmingPassword: false,

        form: this.$inertia.form({
          password: '',
          error: '',
        }, {
          bag: 'confirmPassword',
        })
      }
    },

    methods: {
      startConfirmingPassword() {
        this.form.error = '';

        axios.get(route('password.confirmation').url()).then(response => {
          if (response.data.confirmed) {
            this.$emit('confirmed');
          } else {
            this.confirmingPassword = true;
            this.form.password = '';

            setTimeout(() => {
              this.$refs.password.focus()
            }, 250)
          }
        })
      },

      confirmPassword() {
        this.form.processing = true;

        axios.post(route('password.confirm').url(), {
          password: this.form.password,
        }).then(response => {
          this.confirmingPassword = false;
          this.form.password = '';
          this.form.error = '';
          this.form.processing = false;

          this.$nextTick(() => this.$emit('confirmed'));
        }).catch(error => {
          this.form.processing = false;
          this.form.error = error.response.data.errors.password[0];
        });
      }
    }
  }
</script>
