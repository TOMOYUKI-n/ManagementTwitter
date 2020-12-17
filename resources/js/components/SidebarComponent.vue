<template>
    <div class="">
        <!-- アカウント登録後 -->
        <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length > 0">
            <div class="p-board__top">{{ loginTwitterUser.screen_name }}</div>
            <div class="p-board__plate">
                <div class="p-board__inner">
                    <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                    <div>
                        {{ loginTwitterUser.follows }}
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
                <div class="p-board__inner">
                    <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                    <div>
                        {{ loginTwitterUser.followers }}
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
            </div>
        </div>
        <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
            {{ messageText }}
        </p>
        <!-- 切り替え -->
        <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length === 0">
            <div class="p-board__top">{{ authData }}様</div>
            <div class="p-board__plate">
                <div class="p-board__inner">
                    <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                    <div>
                        ------
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
                <div class="p-board__inner">
                    <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                    <div>
                        ------
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
            </div>
        </div>
        <!-- menu -->
        <div>
            <div v-for="(i, index) in link" :key="index">
                <div class="p-board__link p-board__hover" @click="emitChange(index)"
                    :class="[currentPage === i.id ? 'is-selected' : '']">
                    <div class="p-board__label js_push-sidebar" style="font-size: 13px;">{{ i.label }}</div>
                </div>
            </div>
        </div>
        <footer-component />
    </div>
</template>

<script>
    import { linkParam, twitterAccount } from "../repository";
    import { message } from '../message';
    import axios from "axios";
    export default {
        data() {
            return {
                currentPage: 1,
                link: linkParam,
                authData: {},
                loginTwitterUser: {},
                errorFlg: false,
                authLoginCheck: false,
                messageText: '',
            }
        },
        methods: {
            /**
             * 現在のページをdashboardに渡す
             */
            emitChange(index){
                this.currentPage = index + 1;
                this.$emit('change-page',this.currentPage);
            },
            /**
             * localstorageに現在のページを取得する
             */
            async getCurrentPage() {
                const page = await localStorage.getItem('page');
                if(page === null){
                    this.currentPage = 1;  
                }
                else {
                    this.currentPage = Number(page);
                    this.$emit('change-page',this.currentPage);
                }
            },
            /**
             * APIで、ログインしているユーザ情報を取得しlocalstorageに保存
             */
            async setLoginData(){
                const response = await axios.get(`/api/auth/user`);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    const pastAuthData = await JSON.parse(localStorage.getItem('authData'));
                    if(pastAuthData){
                        const authEmail = pastAuthData.email;
                        const login = await JSON.parse(localStorage.getItem('loginData'));
                        const loginEmail = login.email;
                        // 過去の認証済ユーザーとログインユーザーが一致しているか
                        if (loginEmail === authEmail) {
                            this.authLoginCheck = true;
                        }else {
                            this.authLoginCheck = false;
                        }
                        localStorage.setItem('authData',JSON.stringify(response.data));
                    }else{
                        localStorage.setItem('authData',JSON.stringify(response.data));
                    }
                }
            },
            /**
             * 使用するaccountIdをlocalstorageから取得
             * APIで名前とフォロー人数を取得する
             */
            async getAccountInfo() {
                if(this.authLoginCheck){
                    const storage = await JSON.parse(localStorage.getItem('loginTwitterAccount'));
                    if(storage){
                        const response = await axios.get('/api/twitter/users/' + storage.id);
                        if (response.status === 200) {
                            this.loginTwitterUser = response.data;
                        }
                        else {
                            this.errorFlg = true;
                            this.messageText = message.notGetData;
                        }
                    }
                    else {
                        const storage = await JSON.parse(localStorage.getItem('authData'));
                        this.authData = storage.name;
                    }
                }else{
                    localStorage.removeItem('loginTwitterAccount');
                    const storage = await JSON.parse(localStorage.getItem('authData'));
                    this.authData = storage.name;
                }
            },
        },
        async created() {
            await this.getCurrentPage();
            await this.setLoginData();
            await this.getAccountInfo();
        }
    }
</script>
<style lang="scss" scoped>
</style>