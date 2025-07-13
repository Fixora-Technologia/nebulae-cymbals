@if ($message = Session::get('message'))
    <div class="alert alert-{{ Session::get('alert-type', 'success') }} alert-dismissible fade show" role="alert">
        <i class="fa fa-{{ Session::get('alert-type') === 'danger' ? 'exclamation-triangle' : 'check' }}"></i>
        {{ $message }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
@endif
