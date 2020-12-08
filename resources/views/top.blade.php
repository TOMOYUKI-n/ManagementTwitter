
@extends('layouts.app')

@section('title','Home')

@section('content')

<div class="l-main__top--wrapper">
    <div class="l-main__top--barner">
        <div class="p-top__area--top">
            <div class="p-top__top">
                <div class="p-top__head ">
                    <div class="p-top__top-message">
                        <h1 class="p-top__title">twitter自動集客ツール</h1>
                        
                        <p class="p-top__intro--text">Kamitterを使えば、アクティブユーザーのみをフォローし、予約ツイートで宣伝、複数のアカウントで集客チャネルとしてTwitterを扱えます。</p>
                        <img class="p-top__img__main" src="{{ asset('/images/twitter.svg') }}" alt="twitter">
                    </div>
                </div>
                <div class="p-top__head">
                    <!--ログインエリア-->
                    @guest
                    <div class="p-top__account">
                        <div class="p-top__register">
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
        </div >

        <!--特徴紹介エリア-->
        <div class="p-top__area--section">
            <h1 class="p-top__section-title">3つの特徴</h1>
            <div class="p-top__section-wrap">
                <div class="p-top__section">
                    <img class="p-top__img" src="{{ asset('/images/chat.svg') }}" alt="chanel">
                    <h3 class="p-top__section__subtitle">自動で集客チャネルを作れる</h3>
                    <p class="p-top__section__text">
                        アクティブユーザーのみをフォローし、予約ツイートで宣伝、複数のアカウントで集客チャネルとしてTwitterを扱えます。
                    </p>
                </div>
                <div class="p-top__section">
                    <img class="p-top__img" src="{{ asset('/images/follow.svg') }}" alt="follow">
                    <h3 class="p-top__section__subtitle">アカウントを自動フォロー</h3>
                    <p class="p-top__section__text">
                        自動でアクティブでないユーザーを判断して、フォローするアカウントを整理します。
                        あなたの条件に合ったユーザーを自動でフォローすることも可能です。
                    </p>
                </div>
                <div class="p-top__section">
                    <img class="p-top__img" src="{{ asset('/images/time.svg') }}" alt="booking">
                    <h3 class="p-top__section__subtitle">宣伝ツイートも有効な時間に投稿可能</h3>
                    <p class="p-top__section__text">
                        事前に日時を決めると、自動でツイートができます。
                        いつでも好きな時にツイートが出来ます
                    </p>
                </div>
            </div>
        </div>

        <div class="p-top__area--other">
            <h1 class="p-top__section-title">その他機能</h1>
            <div class="p-top__section-wrap">
                <div class="p-top__section">
                    <i class="fas fa-heart"></i>
                    <h3 class="p-top__section__subtitle">自動いいね機能</h3>
                    <p class="p-top__section__text">
                        特定のキーワードを登録しておくことで、関連するツイートにいいねを自動で行います。より多くの関心のあるユーザーに知ってもらえます。
                    </p>
                </div>
                <div class="p-top__section">
                    <i class="fas fa-user-slash"></i>
                    <h3 class="p-top__section__subtitle">自動アンフォロー機能</h3>
                    <p class="p-top__section__text">
                        自動でアクティブでないユーザーを判断して、フォローするアカウントを整理します。
                        集客対象として有効なアクティブなユーザーとだけ繋がれます。
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="js_toggle_guest">
    <div>
        <div class="p-guest__icon">
            <i class="js_push_guest fas fa-times"></i>
        </div>
        <div class="p-board__link p-board__hover">
            <div class="l-footer__list"><a class="p-top__footer-text" href="/contact">お問い合わせ </a></div>
            <div class="l-footer__list"><a class="p-top__footer-text" href="/term">利用規約 </a></div>
            <div class="l-footer__list"><a class="p-top__footer-text" href="/policy">プライバシーポリシー</a></div>
        </div>
        <div class="l-footer__copyright p-top__footer-text">
            <div>©kamitter2020 kamitter.All Rights Reserved</div>
        </div>
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