@extends('layouts.app')

@section('content')


<div class="l-main__container">
    <div class="p-login__container">

        <form method="POST">
            @csrf
            
            <div class="p-send__section">
                <label for="email" class="p-send__text">{{ __('E-Mail Address') }}</label>
                <div class="p-send__inner">
                    <input id="email" type="email" class="p-send__form @error('email') is-invalid @enderror" name="email" value="{{ $email ?? old('email') }}" required autocomplete="email" autofocus>
                    @error('email')
                        <span class="c-alert__caution2">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="p-send__section">
                <label for="password" class="p-send__text">{{ __('Password') }}</label>
                <div class="p-send__inner">
                    <input id="password" type="password" class="p-send__form @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="c-alert__caution2">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
            </div>
            <div class="p-send__section">
                <label for="password-confirm" class="p-send__text">{{ __('Confirm Password') }}</label>
                <div class="p-send__inner">
                    <input id="password-confirm" type="password" class="p-send__form" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>
            <div class="p-login__button__wrap">
                    <button type="submit" class="p-send__text p-send__button">
                        {{ __('Reset Password') }}
                    </button>
            </div>
        </form>
    </div>
</div>
@endsection
