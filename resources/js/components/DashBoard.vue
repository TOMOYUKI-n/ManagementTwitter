<template>
<div>
    <header-component @isOpen="openSidebar"></header-component>
    <!-- sp -->
    <div class="p-board__wrap">
        <transition name="side" tag="div" v-if="isOpen" appear>
            <div class="p-board__sidebar-sp" key="side">
                <div>
                    <!--  -->
                    <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length > 0">
                        <div class="p-board__top">{{ loginTwitterUser.screen_name }}</div>
                        <div class="p-board__d-flex p-board__top p-board__space-end">
                            <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                            <div>
                                {{ loginTwitterUser.follows }}
                                <small class="p-board__text-small">人</small>
                            </div>
                        </div>
                        <div class="p-board__d-flex p-board__top p-board__space-end">
                            <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                            <div>
                                {{ loginTwitterUser.followers }}
                                <small class="p-board__text-small">人</small>
                            </div>
                        </div>
                    </div>
                    <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
                        {{ messageText }}
                    </p>
                    <!-- 切り替え -->
                    <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length === 0">
                        <div class="p-board__top">{{ authData.name }}</div>
                        <div class="p-board__d-flex p-board__top p-board__space-end">
                            <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                            <div>
                                ------
                                <small class="p-board__text-small">人</small>
                            </div>
                        </div>
                        <div class="p-board__d-flex p-board__top p-board__space-end">
                            <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                            <div>
                                ------
                                <small class="p-board__text-small">人</small>
                            </div>
                        </div>
                    </div>
                    <!-- menu -->
                    <div>
                        <div v-for="(i, index) in link" :key="index">
                            <div class="p-board__link p-board__hover" @click="change(index)"
                                :class="[flgId === i.id ? 'is-selected' : '']">
                                <div style="font-size: 14px;">{{ i.label }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <footer-component></footer-component>
            </div>
        </transition>
        <!-- pc -->
        <div class="p-board__sidebar-pc" >
            <div>
                <!--  -->
                <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length > 0">
                    <div class="p-board__top">{{ loginTwitterUser.screen_name }}</div>
                    <div class="p-board__d-flex p-board__top p-board__space-end">
                        <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                        <div>
                            {{ loginTwitterUser.follows }}
                            <small class="p-board__text-small">人</small>
                        </div>
                    </div>
                    <div class="p-board__d-flex p-board__top p-board__space-end">
                        <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                        <div>
                            {{ loginTwitterUser.followers }}
                            <small class="p-board__text-small">人</small>
                        </div>
                    </div>
                </div>
                <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
                    {{ messageText }}
                </p>
                <!-- 切り替え -->
                <div class="p-board__mp-4" v-if="Object.keys(this.loginTwitterUser).length === 0">
                    <div class="p-board__top">{{ authData.name }}</div>
                    <div class="p-board__d-flex p-board__top p-board__space-end">
                        <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                        <div>
                            ------
                            <small class="p-board__text-small">人</small>
                        </div>
                    </div>
                    <div class="p-board__d-flex p-board__top p-board__space-end">
                        <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                        <div>
                            ------
                            <small class="p-board__text-small">人</small>
                        </div>
                    </div>
                </div>
                <!-- menu -->
                <div>
                    <div v-for="(i, index) in link" :key="index">
                        <div class="p-board__link p-board__hover" @click="change(index)"
                            :class="[flgId === i.id ? 'is-selected' : '']">
                            <div style="font-size: 14px;">{{ i.label }}</div>
                        </div>
                    </div>
                </div>
            </div>
            <footer-component></footer-component>
        </div>
        <div class="p-board__body">
            <section class="p-board__section">
                <transition-group name="t-dashboard_panel" tag="div" class="">
                    <twitter-account key="account" v-if="page === 1" :twitterAccountId="loginTwitterUser.twitter_id" />
                    <panel-follow key="follow" v-if="page === 2" />
                    <panel-unfollow key="unfollow" v-if="page === 3" />
                    <panel-like key="like" v-if="page === 4" />
                    <panel-tweet key="tweet" v-if="page === 5" />
                    <panel-keyword key="keyword" v-if="page === 6" />
                </transition-group>
            </section>
        </div>
    </div>
</div>

</template>

<script>
    import { linkParam, loginUserInfo, twitterAccount } from "../repository";
    import { message } from '../message';
    import axios from "axios";
    export default {
        data() {
            return {
                page: 1,
                flgId:1,
                link: linkParam,
                loginUserInfo: loginUserInfo,
                authData: {},
                loginTwitterUser: {},
                errorFlg: false,
                messageText: '',
                isOpen: false,
            }
        },
        methods: {
            change(index){
                this.flgId = index + 1;
                this.page = this.flgId;
                this.isOpen = false;
            },
            /**
             * APIで、ログインしているユーザ情報を取得しlocalstorageに保存 // ここやらなあかんで！
             */
            async setLoginData(){
                const response = this.loginUserInfo[0];
                this.authData = response;
                localStorage.setItem('authData',JSON.stringify(response));
            },
            /**
             * 使用するaccountIdをlocalstorageから取得
             * APIで名前とフォロー人数を取得する
             */
            async getAccountInfo() {
                // test用
                const storage = await JSON.parse(localStorage.getItem('loginTwitterAccount'));
                if(storage){
                    this.account = storage;
                    const [response] = await twitterAccount.filter(x => x.id === storage.id);
                    this.loginTwitterUser = response;
                }
                else {
                    const storage = await JSON.parse(localStorage.getItem('authData'));
                    this.authData = storage.name;
                }

                // 本番用
                // const storage = await JSON.parse(localStorage.getItem('loginTwitterAccount'));
                // if(storage){
                //     const response = await axios.get('/api/twitter/users/' + storage.id);
                //     if (response.status === 200) {
                //         this.loginTwitterUser = response.data;
                //     }
                //     else {
                //         this.errorFlg = true;
                //         this.messageText = message.notGetData;
                //     }
                // }
                // else {
                //     const storage = await JSON.parse(localStorage.getItem('authData'));
                //     this.authData = storage.name;
                // }
            },
            /**
             * localstorageに現在のページを取得する
             */
            async getCurrentPage() {
                const pages = await localStorage.getItem('page');
                // console.log('pages:' + pages);
                if(pages === null){
                    this.flgId = 1;  
                    this.page = 1;  
                }
                else {
                    this.flgId = Number(pages);
                    this.page = Number(pages);
                }
            },
            /**
             * サイドバーの開閉
             */
            openSidebar() {
                this.isOpen = !this.isOpen;
            }
        },
        async created() {
            await this.getCurrentPage();
            await this.getAccountInfo();
            await this.setLoginData();
        }
    }
</script>
<style lang="scss" scoped>
.on{
    display: block !important;
}
</style>