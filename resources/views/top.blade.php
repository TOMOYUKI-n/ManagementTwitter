
@extends('layouts.app')

@section('title','Home')

@section('content')


<div class="l-main-top-barner">
    <div class="p-top__top">
        <div class="p-top__head">
            <div class="p-top__top-message">
                {{-- <img class="p-top__img__main" src="{{ asset('/images/small_interface.jpg') }}" srcset="{{ asset('/images/small_interface.jpg') }} 1x,{{ asset('/images/interface-croped.jpg') }} 2x" alt="top"> --}}
                <h1 class="p-top__introduction">twitter自動集客ツール</h1>
            </div>
        </div>

        <!--ログインエリア-->
        @guest
        <div class="p-top__account">
            <div class="p-top__register">
                <a class="p-top__text-xs p-botton__login"
                    href="{{ url('login/twitter') }}">
                    {{ __('Twitter Login') }}
                </a>
                <a class="p-top__text-xs p-botton__register__top p-top__mt-3"
                    href="{{ route('register') }}">
                    {{ __('Register Free') }}
                </a>
            </div>
            <div class="p-top__login">
                <p class="p-top__text-xs">既に会員の方はこちら</p>
                <a class="p-top__text-xs p-botton__login p-top__mt-3"
                    href="{{ route('login') }}">
                        {{ __('Login') }}
                </a>
            </div>
        </div>
        @else
        <div></div>
        @endguest
    </div>
</div>

<!--特徴紹介エリア-->
<div class="p-top__section-container">
    <h1 class="p-top__section-title">Crypto Trendの特徴</h1>
    <div class="p-top__section-wrap">
        
        <div class="p-top__section">
            {{-- <img class="p-top__img__trend" src="{{ asset('/images/twitter-crop.jpg') }}" alt="trend"> --}}
            <h3 class="p-top__section__subtitle">仮想通貨のトレンドがわかる</h3>
            <p class="p-top__section__text">
            これひとつでTwitter上にあふれる仮想通貨の情報を収集し、
            トレンドを把握できます。
            </p>
        </div>
        <div class="p-top__section">
            {{-- <img class="p-top__img__follow" src="{{ asset('/images/smartphone.jpg') }}" alt="follow"> --}}
            <h3 class="p-top__section__subtitle">アカウントを自動フォロー</h3>
            <p class="p-top__section__text">
                当サービスでは自動で仮想通貨に関するアカウントを全てフォローできます。
            </p>
        </div>
        <div class="p-top__section">
            {{-- <img class="p-top__img__news" src="{{ asset('/images/newsletter-crop.png') }}" alt="news"> --}}
            <h3 class="p-top__section__subtitle">最新の情報をお届けします</h3>
            <p class="p-top__section__text">
                忙しい合間を縫ってニュースサイトを読み漁る必要はありません。
                最新の仮想通貨に関連するニュースを揃えております。
            </p>
        </div>
    </div>
</div>

@endsection