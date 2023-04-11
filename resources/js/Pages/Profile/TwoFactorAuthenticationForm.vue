<template>
  <jet-action-section>
    <template #title>
      Two Factor Authentication
    </template>

    <template #description>
      Add additional security to your account using two factor authentication.
    </template>

    <template #content>
      <h4 v-if="twoFactorEnabled">
        You have enabled two factor authentication.
      </h4>

      <h4 v-else>
        You have not enabled two factor authentication.
      </h4>

      <div class="mt-3">
        <p>
          When two factor authentication is enabled, you will be prompted for a secure,
          random token during authentication. You may retrieve this token from your phone's Google Authenticator application.
        </p>
      </div>

      <div v-if="twoFactorEnabled">
        <div v-if="qrCode">
          <div class="mt-4">
            <p>
              Two factor authentication is now enabled. Scan the following QR code using your phone's authenticator application.
            </p>
          </div>

          <div class="mt-4" v-html="qrCode">
          </div>
        </div>

        <div v-if="recoveryCodes.length > 0">
          <div class="mt-4">
            <p>
              Store these recovery codes in a secure password manager.
              They can be used to recover access to your account if your two factor authentication device is lost.
            </p>
          </div>

          <div class="py-3 px-5 bg-dark rounded">
            <code class="d-block text-light" v-for="code in recoveryCodes">{{ code }}</code>
          </div>
        </div>
      </div>

      <div class="mt-4">
        <div v-if="! twoFactorEnabled">
          <jet-confirms-password @confirmed="enableTwoFactorAuthentication">
            <jet-button type="button" :class="{ 'opacity-25': enabling }" :disabled="enabling" icon="mdi-toggle-switch">
              Enable
            </jet-button>
          </jet-confirms-password>
        </div>

        <div v-else>
          <jet-confirms-password @confirmed="regenerateRecoveryCodes">
            <jet-secondary-button class="mr-3" icon="mdi-refresh" color="btn-gradient-warning text-dark"
              v-if="recoveryCodes.length > 0">
              Regenerate Recovery Codes
            </jet-secondary-button>
          </jet-confirms-password>

          <jet-confirms-password @confirmed="showRecoveryCodes">
            <jet-secondary-button class="mr-3" v-if="recoveryCodes.length == 0" icon="mdi-eye">
              Show Recovery Codes
            </jet-secondary-button>
          </jet-confirms-password>

          <jet-confirms-password @confirmed="disableTwoFactorAuthentication">
            <jet-button :disabled="disabling" color="btn-gradient-danger" :class="{ 'opacity-25': disabling }" icon="mdi-toggle-switch-off">
              Disable
            </jet-button>
          </jet-confirms-password>
        </div>
      </div>
    </template>
  </jet-action-section>
</template>

<script>
  import JetActionSection from './../../Jetstream/ActionSection'
  import JetButton from './../../Jetstream/Button'
  import JetConfirmsPassword from './../../Jetstream/ConfirmsPassword'
  import JetDangerButton from './../../Jetstream/DangerButton'
  import JetSecondaryButton from './../../Jetstream/SecondaryButton'

  export default {
    components: {
      JetActionSection,
      JetButton,
      JetConfirmsPassword,
      JetDangerButton,
      JetSecondaryButton,
    },

    data() {
      return {
        enabling: false,
        disabling: false,

        qrCode: null,
        recoveryCodes: [],
      }
    },

    methods: {
      enableTwoFactorAuthentication() {
        this.enabling = true

        this.$inertia.post('/user/two-factor-authentication', {}, {
          preserveScroll: true,
        }).then(() => {
          return Promise.all([
            this.showQrCode(),
            this.showRecoveryCodes()
            ])
        }).then(() => {
          this.enabling = false
        })
      },

      showQrCode() {
        return axios.get('/user/two-factor-qr-code')
        .then(response => {
          this.qrCode = response.data.svg
        })
      },

      showRecoveryCodes() {
        return axios.get('/user/two-factor-recovery-codes')
        .then(response => {
          this.recoveryCodes = response.data
        })
      },

      regenerateRecoveryCodes() {
        axios.post('/user/two-factor-recovery-codes')
        .then(response => {
          this.showRecoveryCodes()
        })
      },

      disableTwoFactorAuthentication() {
        this.disabling = true

        this.$inertia.delete('/user/two-factor-authentication', {
          preserveScroll: true,
        }).then(() => {
          this.disabling = false
        })
      },
    },

    computed: {
      twoFactorEnabled() {
        return ! this.enabling && this.$page.user.two_factor_enabled
      }
    }
  }
</script>
