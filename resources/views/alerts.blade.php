<form id="login" class="form-horizontal" role="form" method="POST" action="{{ url('/activate/send') }}">
    {{ csrf_field() }}

    <input type="hidden" id="email" name="email" value="{{ old('email') }}" />

    @if (session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif

    @if (session('warning'))
        <div class="alert alert-warning">
            {{ session('warning') }}

            @if (session('resend'))
                <br>
                <a href="#" onclick="document.forms[0].submit();return false">
                    Send again
                </a>
            @endif
        </div>
    @endif
</form>
