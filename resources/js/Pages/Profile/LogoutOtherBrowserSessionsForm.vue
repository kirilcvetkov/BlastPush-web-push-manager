<template>
  <jet-action-section>
    <template #title>
      Browser Sessions
    </template>

    <template #description>
      Manage and logout your active sessions on other browsers and devices.
    </template>

    <template #content>
      <p>
        If necessary, you may logout of all of your other browser sessions across all of your devices.
        If you feel your account has been compromised, you should also update your password.
      </p>

      <!-- Other Browser Sessions -->
      <div class="list-wrapper mt-4" v-if="sessions.length > 0">
        <ul>
          <li class="border-bottom mb-1" v-for="session in sessions">
            <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24"
              stroke="currentColor" class="text-dark" style="height: 50px;" v-if="session.agent.is_desktop">
              <path d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
            </svg>

            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" fill="none"
              stroke-linecap="round" stroke-linejoin="round" class="text-dark" style="height: 50px;" v-else>
              <path d="M0 0h24v24H0z" stroke="none"></path><rect x="7" y="4" width="10" height="16" rx="1"></rect><path d="M11 5h2M12 17v.01"></path>
            </svg>

            <div class="ml-3">
              <div class="text-sm text-gray-600">
                {{ session.agent.platform }} - {{ session.agent.browser }}
              </div>

              <div>
                <div class="text-xs text-gray-500">
                  {{ session.ip_address }},

                  <span class="text-green-500 font-semibold" v-if="session.is_current_device">This device</span>
                  <span v-else>Last active {{ session.last_active }}</span>
                </div>
              </div>
            </div>
          </li>
        </ul>
      </div>

      <div class="mt-4">
        <jet-button @click.native="confirmLogout" color="btn-gradient-dark" icon="mdi-power">
          Logout Other Browser Sessions
        </jet-button>

        <jet-action-message :on="form.recentlySuccessful">
          Done.
        </jet-action-message>
      </div>

      <!-- Logout Other Devices Confirmation Modal -->
      <jet-dialog-modal :show="confirmingLogout" @close="confirmingLogout = false">
        <template #title>
          Logout Other Browser Sessions
        </template>

        <template #content>
          <p>Please enter your password to confirm you would like to logout of your other browser sessions across all of your devices.</p>

          <jet-input id="password" type="password" label="Password" class="mt-4" ref="password" v-model="form.password"
            @keyup.enter.native="logoutOtherBrowserSessions" :error="form.error('password')"/>
        </template>

        <template #footer>
          <jet-secondary-button @click.native="confirmingLogout = false">
            Nevermind
          </jet-secondary-button>

          <jet-button class="ml-2" @click.native="logoutOtherBrowserSessions" color="btn-gradient-dark" icon="mdi-power"
            :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
            Logout Other Browser Sessions
          </jet-button>
        </template>
      </jet-dialog-modal>
    </template>
  </jet-action-section>
</template>

<script>
  import JetActionMessage from './../../Jetstream/ActionMessage'
  import JetActionSection from './../../Jetstream/ActionSection'
  import JetButton from './../../Jetstream/Button'
  import JetDialogModal from './../../Jetstream/DialogModal'
  import JetInput from './../../Jetstream/Input'
  import JetInputError from './../../Jetstream/InputError'
  import JetSecondaryButton from './../../Jetstream/SecondaryButton'

  export default {
    props: ['sessions'],

    components: {
      JetActionMessage,
      JetActionSection,
      JetButton,
      JetDialogModal,
      JetInput,
      JetInputError,
      JetSecondaryButton,
    },

    data() {
      return {
        confirmingLogout: false,

        form: this.$inertia.form({
          '_method': 'DELETE',
          password: '',
        }, {
          bag: 'logoutOtherBrowserSessions'
        })
      }
    },

    methods: {
      confirmLogout() {
        this.form.password = ''

        this.confirmingLogout = true

        setTimeout(() => {
          this.$refs.password.focus()
        }, 250)
      },

      logoutOtherBrowserSessions() {
        this.form.post(route('other-browser-sessions.destroy'), {
          preserveScroll: true
        }).then(response => {
          if (! this.form.hasErrors()) {
            this.confirmingLogout = false
          }
        })
      },
    },
  }
</script>
