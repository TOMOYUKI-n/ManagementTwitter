@section('header')

    <header class="l-navbar__main-wrap" style="z-index: 1000;">
        <div class="l-navbar__align">
            <div class="l-navbar__sp__d-flex">
                <div class="l-navbar__icon">
                    <i class="fas fa-bars js_push"></i>
                </div>
                <div class="logo">
                    <h1><a class="l-navbar__text" href="/">kamitter</a></h1>
                </div>
            </div>
            <!--PC-->
            <nav>
                @guest
                <ul class="l-navbar__section">
                    <li class="l-navbar__list"><a class="l-navbar__text" href="/login"><p>ログイン</p></a></li>
                    @if (Route::has('register'))
                        <li class="l-navbar__list"><a class="l-navbar__text" href="/register"><p>新規登録</p></a></li>
                    @endif
                </ul>
                @else
                <ul class="l-navbar__section ">
                    <li class="l-navbar__list"><a class="l-navbar__text" href="/dashboard"><p>ダッシュボード</p></a></li>
                    <li class="l-navbar__list">
                        <a class="l-navbar__text" href="{{ route('logout') }}"
                            onclick ="event.preventDefault();
                            document.getElementById('logout-form').submit();">
                            <p>ログアウト</p>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="l-header__sp__form">
                            @csrf
                        </form>
                    </li>
                </ul>
                @endguest
            </nav>
        </div>

        <!--SP-->
        <div class="l-header__sp l-header__sp--left js_toggle" style="z-index: 1000;">

            <div class="l-header__sp__pa-5 js_push">
                <i class="fas fa-times"></i>
            </div>

            @guest
            <ul class="l-header__sp__menu">
                <li class="l-header__sp__menu__list"><a class="l-navbar__text-black" href="/login"><p>ログイン</p></a></li>
                @if (Route::has('register'))
                    <li class="l-header__sp__menu__list"><a class="l-navbar__text-black" href="/register"><p>新規登録</p></a></li>
                @endif
            </ul>
            @else
            <ul class="l-header__sp__menu">
                <li class="l-header__sp__menu__list "><p class="l-navbar__text-black">{{ Auth::user()->name }}</p></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black" href="/account"><p>アカウント登録</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black"　href="/follow"><p>自動フォロー</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black" href="/unfollow"><p>自動アンフォロー</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black" href="/like"><p>自動いいね</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black" href="/tweet"><p>自動予約ツイート</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text-black" href="/keyword"><p>キーワード登録</p></a></li>
                <li class="l-header__sp__menu__list ">
                    <a class="l-navbar__text-black" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form-sp').submit();">
                        <p>ログアウト</p>
                    </a>
                    <form id="logout-form-sp" action="{{ route('logout') }}" method="POST" class="l-header__sp__form">
                        @csrf
                    </form>
                </li>
            </ul>
            <footer class="">
                <ul class="">
                    <li class="l-header__sp__pa25"><a class="l-header__sp__text__sm" href="/contact"><p>お問い合わせ</p></a></li>
                    <li class="l-header__sp__pa25"><a class="l-header__sp__text__sm" href="/term"><p>利用規約</p></a></li>
                    <li class="l-header__sp__pa25"><a class="l-header__sp__text__sm" href="/policy"><p>プライバシーポリシー</p></a></li>
                </ul>
                <div class="l-header__sp__copy l-header__sp__text__sm">
                    <div>©kamitter2020 kamitter.All Rights Reserved</div>
                </div>
            </footer>
            @endguest
        </div>
    </header>
@show