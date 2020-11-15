<template>
    <div class="l-contents">

        <div class="p-contents__area--narrow">
            <h2 class="p-contents__head"><i class="c-icon--twitter fab fa-twitter"></i>利用するTwitterアカウントを選択する</h2>
            <div v-show="isMaximumAccount" class="">
                <a class="p-botton__account__add" href="auth/twitter/oauth">
                    <p class="">
                        <i class="c-icon__twitter far fa-plus-square c-icon__mr-2"></i>Twitterアカウントの追加
                    </p>
                </a>
            </div>
            <ul class="p-twitter">
                <transition-group name="t-twitter_card">
                    <account-card
                            v-for="(user, index) in users"
                            :key="user.twitter_id"
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
                        <div type="submit" class="p-botton__delete p-form__half-btn width__three" @click="deleteOn = false">
                            <i class="fas fa-times m__r2"></i>
                            <div>キャンセル</div>
                        </div>
                        <div type="submit" class="p-botton__delete  p-form__half-btn width__three" @click="removeCard">
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

    import { twitterAccount } from '../repository'

    export default {
        props: {
            twitterAccountId: 0,
        },
        data() {
            return {
                users: [],
                accountNum: 0,
                deleteOn: false,
                error: '',
                message: {
                    connect: '接続ができませんでした。再度実行してください。',
                    disconnect: '解除ができませんでした。再度実行してください。',
                    status: '連携状況が取得できませんでした。再度実行してください。'
                },
            }
        },
        methods: {
            /**
             * ユーザーが登録しているTwitterUserのID一覧を取得する
             */
            async fetchTwitterUsers() {
                const response = twitterAccount;
                this.users = response;
                this.accountNum = response.length;

            },
            /**
             * TwitterCardのemitをトリガーにして
             * TwitterUserのカードを配列から削除する
             */
            removeCard() {
                this.users.splice(this.deleteTarget, 1);
                this.deleteOn = false;
            },
            deleteModal(emitObject) {
                this.deleteOn = true;
                this.deleteTarget = emitObject.index;
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
            await this.fetchTwitterUsers()
        }
    }
</script>