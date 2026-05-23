@if(auth()->user()->hasRole('admin'))
    @php header('Location: '.route('admin.dashboard')); exit; @endphp
@else
    @php header('Location: '.route('dashboard')); exit; @endphp
@endif
