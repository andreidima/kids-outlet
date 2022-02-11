@if ($errors->any())
    <div class="alert alert-danger mb-4" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{  $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session()->has('status') || session()->has('success'))
    <div class="alert alert-success mb-2" role="alert">
        {{ session('status') }}
        {{ session('succes') }}
        {{ session('success') }}
    </div>
@elseif (session()->has('eroare') || session()->has('error'))
    <div class="alert alert-danger mb-2" role="alert">
        {{ session('eroare') }}
        {{ session('error') }}
    </div>
@elseif (session()->has('atentionare') || session()->has('warning'))
    <div class="alert alert-warning mb-2" role="alert">
        {{ session('atentionare') }}
        {{ session('warning') }}
    </div>
@endif
