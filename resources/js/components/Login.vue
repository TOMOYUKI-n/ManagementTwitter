<template>
    <div class="l-main__container">
        <div id="login" class="p-login__container">
            <div>
                <!--入力エリア-->
                <div class="p-login__inner">
                    <i class="fas fa-user"></i>
                    <input
                        type="text"
                        class="p-login__form"
                        v-model="email"
                        @input="validateEmail"
                        placeholder="メールアドレス"
                    />
                </div>
                <div class="p-login__inputError" v-if="errors.email">{{ errors.email }}</div>
                <div class="p-login__inputError" v-if="emailError">{{ emailErrorText }}</div>

                <div class="p-login__inner">
                    <i class="fas fa-unlock-alt"></i>
                    <input
                        type="password"
                        class="p-login__form"
                        v-model="password"
                        placeholder="パスワード"
                    />
                </div>
                <div class="p-login__inputError" v-if="errors.password">{{ errors.password }}</div>

                <!--ボタンエリア-->
                <div class="p-login__button__wrap">
                    <a
                        @click="login"
                        class="p-button__login"
                        :class="emailError? 'p-login__disabled':''"
                    >
                        ログイン</a>
                </div>

                <p class="p-login__text--center">アカウントをお持ちで無い方はこちらから</p>

                <div class="p-login__button__wrap" @click="register">
                    <a
                        class="p-button__register__top"
                        :class="emailError? 'p-login__disabled':''"
                    >
                        新規登録</a>
                </div>

                <div class="p-login__button__wrap">
                    <p class="p-login__forgot" @click="passlost">パスワードを忘れた方はこちら</p>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from "axios";

export default {
    data: () => {
      return {
          email: "",
          password: "",
          errors: {},
          message: "",
          emailError: false,
      };
    },
    methods: {
        login() {
            if (!this.emailError) {
                // データの保存
                this.saveLoginData();
                // 送信
                const url = "/login";
                const params = {
                    email: this.email,
                    password: this.password,
                };
                axios
                    .post(url, params)
                    .then((response) => {
                        location.href = "/dashboard";
                    })
                    .catch((error) => {
                        // エラー時のコメントをlaravelからキャッチする
                        const responseErrors = error.response.data.errors;
                        const errorsData = {};
                        for (let key in responseErrors) {
                            errorsData[key] = responseErrors[key][0];
                        }
                        this.errors = errorsData;
                    }
                );
            }
        },
        register() {
            location.href = "/register";
        },
        passlost() {
            location.href = "/password/reset";
        },
        saveLoginData() {
            // データの保存
            const loginArray = { email: this.email, password: this.password };
            const loginData = JSON.stringify(loginArray);
            localStorage.setItem("loginData", loginData);
        },
        getLoginData() {
            const loginData = localStorage.getItem("loginData");
            const loginArray = JSON.parse(loginData);
            if(loginArray.email === null) return;
            this.email = loginArray.email;
            this.password = loginArray.password;
        },
        validateEmail() {
            const regexp = /^[A-Za-z0-9]{1}[A-Za-z0-9_.-]*@{1}[A-Za-z0-9_.-]{1,}\.[A-Za-z0-9]{1,}$/;
            regexp.test(this.email) ? this.emailError = false: this.emailError = true;
        }
    },
    computed: {
        emailErrorText () {
            this.errors = {};
            return this.emailError ? '無効なメールアドレスです' : '';
        }
    },
    mounted() {
        this.getLoginData();
    },
};
</script>
<style lang="scss" scoped>

</style>