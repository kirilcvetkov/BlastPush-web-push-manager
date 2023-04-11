<template>
  <app-layout>
    <template #title>Profile</template>
    <template #icon>
      <i class="mdi mdi-account mdi-18px"></i>
    </template>

    <update-profile-information-form :user="$page.user" class="pb-3" />

    <jet-section-border />

    <update-password-form class="py-3" />

    <jet-section-border />

    <two-factor-authentication-form class="py-3" v-if="$page.jetstream.canManageTwoFactorAuthentication" />

    <jet-section-border />

    <logout-other-browser-sessions-form :sessions="sessions" class="py-3" />

    <jet-section-border />

    <delete-user-form class="py-3" />
  </app-layout>
</template>

<script>
  import AppLayout from './../../Layouts/AppLayout'
  import DeleteUserForm from './DeleteUserForm'
  import JetSectionBorder from './../../Jetstream/SectionBorder'
  import LogoutOtherBrowserSessionsForm from './LogoutOtherBrowserSessionsForm'
  import TwoFactorAuthenticationForm from './TwoFactorAuthenticationForm'
  import UpdatePasswordForm from './UpdatePasswordForm'
  import UpdateProfileInformationForm from './UpdateProfileInformationForm'

  export default {
    props: ['sessions'],

    components: {
      AppLayout,
      DeleteUserForm,
      JetSectionBorder,
      LogoutOtherBrowserSessionsForm,
      TwoFactorAuthenticationForm,
      UpdatePasswordForm,
      UpdateProfileInformationForm,
    },
  }
</script>
