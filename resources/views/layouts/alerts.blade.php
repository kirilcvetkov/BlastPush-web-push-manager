
@if (Session::has('success') || isset($success))
  <div class="alert alert-success alert-dismissible fade show" role="alert">
    {{ Session::get('success') ?? $success }}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if (isset($warning) && ! empty($warning))
  <div class="alert alert-warning alert-dismissible fade show" role="alert">
    {!! $warning !!}
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>
@endif

@if ($errors->any())
  {!! implode('', $errors->all('  <div class="alert alert-danger alert-dismissible fade show" role="alert">
    :message
    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
      <span aria-hidden="true">&times;</span>
    </button>
  </div>')) !!}
@endif
