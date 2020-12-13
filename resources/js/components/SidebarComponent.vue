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
            <div class="p-board__top">{{ authData.name }}</div>
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
             * 使用するaccountIdをlocalstorageから取得
             * APIで名前とフォロー人数を取得する
             */
            async getAccountInfo() {
                // test用
                // const storage = await JSON.parse(localStorage.getItem('loginTwitterAccount'));
                // if(storage){
                //     this.account = storage;
                //     const [response] = await twitterAccount.filter(x => x.id === storage.id);
                //     this.loginTwitterUser = response;
                // }
                // else {
                //     const storage = await JSON.parse(localStorage.getItem('authData'));
                //     this.authData = storage.name;
                // }
                // this.$emit('twitter-id',this.loginTwitterUser);

                // 本番用
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
                    this.authData = response.data;
                    localStorage.setItem('authData',JSON.stringify(response.data));
                }
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
        },
        async created() {
            await this.getCurrentPage();
            await this.getAccountInfo();
            await this.setLoginData();
        }
    }
</script>
<style lang="scss" scoped>
</style>