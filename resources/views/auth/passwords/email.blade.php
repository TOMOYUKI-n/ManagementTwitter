@extends('layouts.app')

@section('content')


<div class="l-main__container">
    <div >
        <!-- タイトル -->
            <div class="l-main__title">
            {{ __('Reset Password') }}
            </div>
            <div class="p-login__container">
                @if (session('status'))
                    <div class="c-alert__success">
                        {{ session('status') }}
                    </div>
                @endif
                <form method="POST" action="{{ route('password.email') }}">
                    @csrf
                    <div style="display: block;">
                        <label for="email" class="p-send__text">{{ __('E-Mail Address') }}</label>
                        <div class="p-regist__inner">
                            <input id="email" type="email" class="p-send__form @error('email') is-invalid @enderror"
                                    name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                            @error('email')
                                <span class="error_mail_message">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                    </div>
                    <div class="p-login__button__wrap">
                            <button type="submit" class="p-send__text p-send__button">
                                {{ __('Send Password Reset Link') }}
                            </button>
                    </div>
                </form>
            </div>
    </div>
</div>
@endsection
