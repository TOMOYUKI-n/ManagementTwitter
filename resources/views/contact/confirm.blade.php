@extends('layouts.app')

@section('content')
<div class="l-main__container">
    <div id="login" class="p-login__container">

        <form method="POST" action="{{ route('contact.send') }}">
            @csrf
            <div class="p-contact__inner">
                <div>メールアドレス</div>
                <div class="p-contact__text">
                    {{ $inputs['email'] }}
                </div>
                <input type="hidden" class="p-login__form" name="email" value="{{ $inputs['email'] }}">
            </div>
            <div class="p-contact__inner">
                <div>タイトル</div>
                <div class="p-contact__text">
                    {{ $inputs['title'] }}
                </div>
                <input type="hidden" class="p-login__form" name="title" value="{{ $inputs['title'] }}">
            </div>

            <div class="p-contact__inner">
                <div>お問い合わせ内容</div>
                <div class="p-contact__text p-contact__textarea">
                    {!! nl2br(e($inputs['body'])) !!}
                </div>
                <input type="hidden" class="p-login__form" name="body" value="{{ $inputs['body'] }}">
            </div>

            <div class="p-top__login">
                <button type="submit" name="action" value="back" class="p-login__text p-btn__login">
                    入力内容修正
                </button>
            </div>
            <div class="p-top__login">
                <button type="submit"  name="action" value="submit"  class="p-login__text p-btn__submit">
                    送信する
                </button>
            </div>
            
        </form>
    </div>
</div>
@endsection
