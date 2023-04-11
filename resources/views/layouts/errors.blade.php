<div class="content-wrapper d-flex align-items-center text-center error-page bg-{{ $bg ?? 'primary' }}">
  <div class="row flex-grow">
    <div class="col-lg-9 mx-auto text-white">
      <div class="row align-items-center d-flex flex-row">
        <div class="col-lg-6 text-lg-right pr-lg-4">
          <h1 class="display-1 mb-0">{{ $code }}</h1>
        </div>
        <div class="col-lg-6 error-page-divider text-lg-left pl-lg-4">
          <h2>SORRY!</h2>
          <h3 class="font-weight-light">{{ $message }}</h3>
        </div>
      </div>
      <div class="row mt-5">
        <div class="col-12 text-center mt-xl-2">
          <a class="btn btn-gradient-light btn-lg" href="{{ url('/') }}">
            <i class="mdi mdi-chevron-left"></i> Home
          </a>
        </div>
      </div>
    </div>
  </div>
</div>
