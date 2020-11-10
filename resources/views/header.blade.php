@section('header')

    <header class="l-navbar__main-wrap" style="z-index: 1000;">

        <div class="logo">
            <h1 class="logo__pad">
                <a class="l-navbar__text" href="/">Mytests</a>
            </h1>
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
                <li class="l-navbar__list"><a class="l-navbar__text" href="/news"><p>NEWS一覧</p></a></li>
                <li class="l-navbar__list"><a class="l-navbar__text"　href="/accountList"><p>アカウント一覧</p></a></li>
                <li class="l-navbar__list"><a class="l-navbar__text" href="/account"><p>twitter連携</p></a></li>
                <li class="l-navbar__list"><p class="l-navbar__text" >{{ Auth::user()->name }}</p></li>
                <li class="l-navbar__list"><a class="l-navbar__text" href="/contact"><p>お問い合わせ</p></a></li>
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
        <div class="sp__icon__container js_push">
            <i class="fas fa-bars l-navbar__icon "></i>
        </div>

        <!--SP-->
        <div class="l-header__sp l-header__sp--right js_toggle" style="z-index: 1000;">

            <div class="l-header__sp__icon__container js_push">
                <i class="fas fa-times l-header__sp__icon"></i>
            </div>

            @guest
            <ul class="l-header__sp__menu">
                <li class="l-header__sp__menu__list"><a class="l-navbar__text" href="/login"><p>ログイン</p></a></li>
                @if (Route::has('register'))
                    <li class="l-header__sp__menu__list"><a class="l-navbar__text" href="/register"><p>新規登録</p></a></li>
                @endif
            </ul>
            @else
            <ul class="l-header__sp__menu">
                <li class="l-header__sp__menu__list "><p class="l-navbar__text">{{ Auth::user()->name }}</p></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text" href="/news"><p>NEWS一覧</p></a></li>
                <li class="l-header__sp__menu__list"><a class="l-navbar__text"　href="/accountList"><p>アカウント一覧</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text" href="/account"><p>twitter連携</p></a></li>
                <li class="l-header__sp__menu__list "><a class="l-navbar__text" href="/contact"><p>お問い合わせ</p></a></li>
                <li class="l-header__sp__menu__list ">
                    <a class="l-navbar__text" href="{{ route('logout') }}"
                        onclick="event.preventDefault();
                        document.getElementById('logout-form-sp').submit();">
                        <p>ログアウト</p>
                    </a>
                    <form id="logout-form-sp" action="{{ route('logout') }}" method="POST" class="l-header__sp__form">
                        @csrf
                    </form>
                </li>
            </ul>
            @endguest
        </div>
    </header>
@show