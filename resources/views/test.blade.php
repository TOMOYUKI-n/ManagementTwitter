@extends('layouts.app')

@section('content')
<div class="l-main__container">
    <div class="p-verify__container">
        <!-- タイトル -->
        <div class="l-main__title">
            {{ __('Verify Your Email Address') }}
        </div>

        <!-- 内容 -->
        <div class="p-login__inner">
            

            <div class="p-verify__text">
                <p>
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                </p>
            </div>
            <div class="p-verify__here">
                <a class="p-login__alarttext">
                    {{ __('If you did not receive the email click here to request another') }}
                </a>
            </div>
        </div>
    </div>
</div>

@endsection