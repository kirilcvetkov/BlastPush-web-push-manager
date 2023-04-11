<div class="container-fluid page-body-wrapper full-page-wrapper">
  <div class="content-wrapper-login d-flex align-items-center auth">
    <div class="flex-grow mx-auto" style="max-width: 600px;">
      <div class="auth-form-light text-left p-5">
        <div class="brand-logo text-primary text-center display-3">
          {{ $logo }}
        </div>

        {{ $slot }}
      </div>
    </div>
  </div>
</div>
