@section('header')

    <header class="l-navbar__main-wrap">
        <div class="l-navbar__align">
            <div>
                @guest
                <div class="l-navbar__sp__d-flex">
                    <div class="l-navbar__icon">
                        <i class="fas fa-bars js_push_guest"></i>
                    </div>
                    <div class="logo">
                        <h1><a class="l-navbar__text" href="/">Kamitter</a></h1>
                    </div>
                </div>
                @else
                <div class="l-navbar__sp__d-flex">
                    <div class="l-navbar__icon">
                        <i class="fas fa-bars js_push-sidebar"></i>
                    </div>
                    <div class="logo">
                        <h1><a class="l-navbar__text" href="/">Kamitter</a></h1>
                    </div>
                </div>
                @endguest
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
                <ul class="l-navbar__section">
                    <li class="l-navbar__list"><a class="l-navbar__text" href="/dashboard"><p><i class="fas fa-home"></i></p></a></li>
                    <li class="l-navbar__list">
                        <a class="l-navbar__text" href="{{ route('logout') }}"
                            onclick ="event.preventDefault();
                            document.getElementById('logout-form').submit();
                            localStorage.removeItem('loginTwitterAccount');
                            localStorage.removeItem('authData');">
                            <p><i class="fas fa-sign-out-alt"></i></p>
                        </a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" class="l-header__sp__form">
                            @csrf
                        </form>
                    </li>
                </ul>
                @endguest
            </nav>
        </div>

    </header>
    @guest
    <div class="p-board__sidebar-sp js_toggle_guest">
        <div class="">
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
    @else
    <div class="p-board__navi-sp js_open-navi">
        <div class="">
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
    @endguest
@show