@extends('layouts.app')

@section('content')
<div class="l-main__contact">
    <div id="login" class="p-login__container">

        <form method="POST" action="{{ route('contact.confirm') }}">
            @csrf
            <div class="p-send__inner">
                <input id="email" type="email" class="p-send__form @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" placeholder="メールアドレス" required>
                @error('email')
                    <span class="p-login__inputError">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="p-send__inner">
                <input id="title" type="text" class="p-send__form @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="タイトル"　required>
                @error('title')
                    <span class="p-login__inputError">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>
            <div class="p-send__inner">
                <textarea name="body" class="p-send__form" style="min-height: 130px;" placeholder="お問い合わせ内容">{{ old('body') }}</textarea>
                @if ($errors->has('body'))
                    <p class="p-login__inputError">{{ $errors->first('body') }}</p>
                @endif
            </div>
            <!--通常のログイン-->

            <div class="p-login__button__wrap">
                <button type="submit" class="p-send__text p-send__button">
                    入力内容確認
                </button>
            </div>
            
        </form>
    </div>
</div>
<div class="p-top__footer">
    <div>
        <div class="p-top__footer-link">
            <div class="l-footer__list"><a class="p-top__footer-text" href="/contact">お問い合わせ </a></div>
            <div class="l-footer__list"><a class="p-top__footer-text" href="/term">利用規約 </a></div>
            <div class="l-footer__list"><a class="p-top__footer-text" href="/policy">プライバシーポリシー</a></div>
        </div>
        <div class="p-top__footer-text p-top__pc-footer-text">
            <div>©kamitter2020 kamitter.All Rights Reserved</div>
        </div>
    </div>
</div>
@endsection
