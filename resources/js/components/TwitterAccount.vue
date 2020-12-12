<template>
    <div class="l-contents">

        <div class="p-contents__area--narrow">
            <h2 class="p-contents__head"><i class="c-icon--twitter fab fa-twitter"></i>利用するTwitterアカウントを選択する</h2>
            <div v-show="isMaximumAccount" class="">
                <a class="p-button__account__add" @click="twitterLogin">
                    <p class="">
                        <i class="c-icon__twitter far fa-plus-square c-icon__mr-2"></i>Twitterアカウントの追加
                    </p>
                </a>
            </div>
            <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
                {{ messageText }}
            </p>
            <ul class="p-twitter">
                <transition-group name="t-twitter_card">
                    <account-card
                            v-for="(user, index) in accounts"
                            :key="user.id"
                            :item="user"
                            :index="index"
                            @delUser="deleteModal"
                            :selectId="twitterAccountId"
                    />
                </transition-group>
            </ul>
            <section class="p-modal p-modal--opened" v-show="deleteOn">
                <div class="p-modal__contents">
                    <p class="p-form__delete">本当に削除しますか？</p>
                    <div class="p-form__delete__wrap">
                        <div type="submit" class="p-button__delete p-form__half-btn width__three" @click="deleteOn = false">
                            <i class="fas fa-times m__r2"></i>
                            <div>キャンセル</div>
                        </div>
                        <div type="submit" class="p-button__delete  p-form__half-btn width__three" @click="removeCard">
                            <i class="fas fa-check m__r2"></i>
                            <div>削除</div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
    </div>
</template>

<script>

    import { twitterAccount } from '../repository';
    import { message } from '../message';
    import axios from "axios";
    export default {
        props: {
            twitterAccountId: 0,
        },
        data() {
            return {
                page: 1,
                accounts: [],
                accountNum: 0,
                deleteOn: false,
                errorFlg: false,
                messageText: '',
                deleteTarget: 0,
                deleteTargetTwitterId: 0
            }
        },
        methods: {
            /**
             * ユーザーが登録しているTwitterUserのID一覧を取得する
             */
            async fetchTwitterUsers() {
                try {
                    const response = await axios.get('/api/twitter/users/list');
                    //const response = await axios.get('/test/twitter/users/list');//テスト用
                    if (response.status === 200) {
                        this.accounts = response.data.accounts;
                        this.accountNum = response.data.accounts_num;
                    }                    
                }
                catch (error) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
            },
            /**
             * TwitterCardのemitをトリガーにして
             * TwitterUserのカードを配列から削除する
             */
            async removeCard() {
                const res = await this.deleteTwitterUser();

                if(res){
                    // 削除の描画
                    this.accounts.splice(this.deleteTarget, 1);
                    this.deleteOn = false;
                    // 再描画
                    this.$emit('user-delete',this.deleteTarget);
                    await this.fetchTwitterUsers();
                }
                else{
                    this.errorFlg = true;
                    this.messageText = message.notDelete;
                }

            },
            deleteModal(emitObject) {
                this.deleteOn = true;
                this.deleteTarget = emitObject.index;
                this.deleteTargetTwitterId = emitObject.item_id;
            },
            /**
             * TwitterUserIdをlocalstorage,DBから削除する
             * @returns boolean
             */
            async deleteTwitterUser() {
                const response = await axios.delete(`/api/twitter/users/${this.deleteTargetTwitterId}`)
                if (response.status !== 200) {
                    return false;
                }
                else {
                    return true;
                }
            },
            twitterLogin(){
                location.href = "/login/twitter";
            },
            /**
             * localstorageから現在のページを保存する
             */
            setCurrentPage() {
                localStorage.setItem('page', this.page);
            }
        },
        computed: {
            //TwitterUserアカウントを追加するボタンの非表示フラグ
            isMaximumAccount() {
                return this.accountNum < 10
            }
        },
        //ページ作成時に実行
        async created() {
            await this.fetchTwitterUsers();
            this.setCurrentPage();
        }
    }
</script>