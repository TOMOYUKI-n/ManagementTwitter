<template>
<div>

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
</div>

</template>

<script>
    import { linkParam, loginUserInfo, twitterAccount } from "../repository";
    import { message } from '../message';
    import axios from "axios";
    export default {
        props: {
            loginTwitterUser: {},
            errorFlg: false,
            messageText: '',
            flgId: 0,
            // 仮データ
            loginUserInfo: {},
        },
        data() {
            return {
                link: linkParam,
                authData: {},
            }
        },
        methods: {
            change(index){
                this.flgId = index + 1;
                this.page = this.flgId;
                this.isOpen = false;
            },
            /**
             * APIで、ログインしているユーザ情報を取得しlocalstorageに保存
             */
            async setLoginData(){
                const response = this.loginUserInfo[0];
                this.authData = response;
                localStorage.setItem('authData',JSON.stringify(response));
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