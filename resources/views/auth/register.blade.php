@extends('layouts.app')

@section('content')

<div class="l-main__container">
    <div class="p-login__container">

        <div class="l-main__title p-regist__potision-center">
            {{ __('New Register') }}
        </div>
        <form method="POST" action="{{ route('register') }}">
            @csrf
            <div>
                <div class="p-login__inner">
                    <input id="name" type="text" placeholder="ニックネーム" class="p-regist__form @error('name') is-invalid @enderror" name="name" value="{{ old('name') }}" required autocomplete="name" autofocus>
                    @error('name')
                        <span class="p-login__inputError">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="p-login__inner">
                    <input id="email" type="email" placeholder="メールアドレス" class="p-regist__form @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email')
                        <span class="p-login__inputError">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <p class="p-regist__caution">※パスワードは8文字以上、半角英数文字でご入力ください</p>
                <div class="p-login__inner">
                    <input id="password" type="password" placeholder="パスワード"　class="p-regist__form @error('password') is-invalid @enderror" name="password" required autocomplete="new-password">
                    @error('password')
                        <span class="p-login__inputError">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>

                <div class="p-login__inner">
                    <input id="password-confirm" type="password" placeholder="確認用パスワード" class="p-regist__form" name="password_confirmation" required autocomplete="new-password">
                </div>
            </div>
            <div class="p-login__button__wrap">
                <button type="submit" class="p-botton__register__new">
                    {{ __('Register') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
