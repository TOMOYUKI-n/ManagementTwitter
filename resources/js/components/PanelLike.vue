<template>
    <div class="p-panel u-color__bg--white">

        <div class="p-status">
            <p v-show="showRunButton" class="p-status__show p-status__sleep" style="background-color: #3335;">{{serviceStatusLabel}}</p>
            <p v-show="showStopButton" class="p-status__show p-status__active">{{serviceStatusLabel}}</p>
            <button class="c-button c-button__status--on"
                    @click="serviceSwitch = true"
                    v-show="showRunButton">
                    <i class="fas fa-power-off c-icon__mr-2"></i>稼働
            </button>
            <button class="c-button c-button__status--off"
                    @click="serviceSwitch = true"
                    v-show="showStopButton">
                    <i class="fas fa-ban c-icon__mr-2"></i>停止
            </button>
        </div>

        <div class="p-table__title">
            <div class="p-table__sp__title">
                <h2 class="p-table__caption">自動いいねリスト</h2>
                <p class="p-table__caption__text">※キーワードを設定することで、該当するツイートに自動でいいねを送ります。</p>
            </div>
            <button class="c-button c-button--add" @click="newModal = ! newModal">
                <i class="c-icon__mr-2 c__color--blue fas fa-plus"></i>
                自動いいねの設定を追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--like">いいね条件</th>
                <th class="p-table__th p-table__th--like">操作</th>
            </tr>

            <tr v-for="(like, index) in likes" :key="index">
                <td class="p-table__td">{{like.keyword.word}}</td>
                <td class="p-table__td">
                    <div class="p-table__action">
                        <div class="p-table__btn-wrap">
                            <button class="c-button c-button--twitter p-table__button"
                                    @click="showEditModal(like)"
                            >
                                <i class="c__color--blue fas fa-pen p-table__test-xs"></i>
                            </button>
                            <button class="c-button c-button--delete p-table__button c-button--delete "
                                    @click="remove(like, index)"
                            >
                                <i class="fas fa-trash-alt p-table__test-xs"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <p v-show="likes.length === 0" style="font-size: 14px; margin-top: 8px;">
            データがありません
        </p>
        <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
            {{ messageText }}
        </p>

        <div class="p-modal__wrapper">
            <section class="p-modal" v-show="newModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="newModal = !newModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <form class="p-form" @submit.prevent="addLike">
                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
                        <label class="p-form__label" for="add-like-keyword">いいね条件の選択 *必須</label>
                        <select class="p-form__select" id="add-like-keyword"
                                v-model="addForm.keyword_id"
                                required
                        >
                            <option v-for="keyword in keywords" :key="keyword.id" :value="keyword.id">{{keyword.merged_word}}</option>
                        </select>

                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--twitter">追加</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal" v-show="editModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="editModal = !editModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <form class="p-form" @submit.prevent="editLike">

                        <label class="p-form__label" for="edit-like-keyword">いいね条件の選択 *必須</label>
                        <select class="p-form__select" id="edit-like-keyword"
                                v-model="editForm.keyword_id"
                                required
                        >
                            <option v-for="keyword in keywords" :key="keyword.id" :value="keyword.id">{{keyword.merged_word}}</option>

                        </select>
                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--twitter">変更</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal p-modal--opened" v-show="serviceSwitch">
                <div class="p-modal__contents">
                    <p class="p-form__delete">自動化サービスを利用しますか？</p>
                    <div class="p-form__delete__wrap">
                        <div type="submit" class="c-button p-form__half-btn width__three" @click="serviceSwitch = false">
                            <i class="fas fa-times m__r2"></i>
                            <div>キャンセル</div>
                        </div>
                        <div v-show="showRunButton" type="submit" class="c-button p-status__active p-form__half-btn width__three" @click="runLikeService">
                            <i class="fas fa-check m__r2"></i>
                            <div>開始する</div>
                        </div>
                        <div v-show="showStopButton" type="submit" class="c-button p-status__sleep p-form__half-btn width__three" @click="stopLikeService">
                            <i class="fas fa-check m__r2"></i>
                            <div>停止する</div>
                        </div>
                    </div>
                </div>
            </section>

            <section class="p-modal p-modal--opened" v-show="deleteOn">
                <div class="p-modal__contents">
                    <p class="p-form__delete">本当に削除しますか？</p>
                    <div class="p-form__delete__wrap">
                        <div type="submit" class="c-button p-form__half-btn width__three" @click="deleteOn = false">
                            <i class="fas fa-times m__r2"></i>
                            <div>キャンセル</div>
                        </div>
                        <div type="submit" class="p-botton__delete  p-form__half-btn width__three" @click="removeLike">
                            <i class="fas fa-check m__r2"></i>
                            <div>削除</div>
                        </div>
                    </div>
                </div>
            </section>

        </div>
        <div class="c-button--add--wrap">
            <button class="c-button--add--sp" @click="newModal = ! newModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>

    </div>
</template>

<script>
    import { filterWords, targetAccountList, manegementServiceStatus } from "../repository"
    import { message } from '../message';
    import axios from "axios";
    export default {
        data() {
            return {
                page: 4,
                twitter_id: 0,
                errorFlg: false,
                messageText: '',
                serviceSwitch: false,
                deleteOn: false,
                deleteIndex: 0,
                deleteItem: [],
                likes: [],
                keywords: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                errors: null,
                addForm: {
                    keyword_id: null,
                },
                editForm: {
                    id: null,
                    keyword_id: null,
                },
            }
        },
        computed: {
            /**
             * フィルターキワードの追加、変更、削除イベントの通知を取得する
             */
            // dashChange() {
            //     return this.$store.state.dashboard.noticeToLike
            // },
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            }
        },
        methods: {
            /**
             * 登録した自動いいねデータ一覧を取得する
             */
            async fetchLikes() {
                const response = await axios.get(`/api/like/list/${this.twitter_id}`);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.likes = response.data;
                }
            },
            /**
             * キーワード一覧を取得する
             */
            async fetchKeywords() {
                const response = await axios.get('/api/keyword');
                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                this.keywords = response.data;
            },
            /**
             * 新規自動いいねを追加する
             * addFormはkeyword_idのみ
             */
            async addLike() {
                const response = await axios.post(`/api/like/${this.twitter_id}`, this.addForm);
                console.log(response);
                if (response.status !== 200 || response.data === 500) {
                    this.newModal = false;
                    this.errorFlg = true;
                    this.messageText = message.notUpdate;
                }
                if (response.data === 200) {
                    this.newModal = false;
                    // 再描画
                    await this.fetchLikes();
                    await this.fetchKeywords();
                }
            },

            /**
             * 編集用のモーダルフォームを表示する
             * 表示の際に自動いいねのデータを入力しておく
             */
            showEditModal(like) {
                this.editModal = true;
                this.editForm.id = like.id;
                this.editForm.keyword_id = like.keyword_id;
            },

            /**
             * 自動いいねデータを編集する
             */
            async editLike() {
                const response = await axios.put(`/api/like/${this.twitter_id}`, this.editForm);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.resetEditForm();
                }
                if (response.data === 200) {
                    // 再描画
                    this.resetEditForm();
                    await this.fetchLikes();
                    await this.fetchKeywords();
                }
            },
            /**
             * 削除モーダル表示、indexを取得
             */
            remove(item, index) {
                this.deleteOn = true;
                this.deleteIndex = index;
                this.deleteItem = item;
            },
            /**
             * 自動いいねを削除する
             */
            async removeLike() {
                const response = await axios.post(`/api/like/delete/${this.twitter_id}`, this.deleteItem);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.deleteOn = false;
                }
                if (response.data === 200) {
                    this.deleteOn = false;
                    this.likes.splice(this.deleteIndex, 1);
                    // 再描画
                    await this.fetchLikes();
                    await this.fetchKeywords();
                }
            },

            /**
             * フォームを初期化
             */
            resetEditForm() {
                this.editModal = false;
                this.editForm.id = null;
                this.editForm.keyword_id = null;
            },

            /**
             * 自動いいねサービスのステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get(`/api/system/status/${this.twitter_id}`);
                // console.log(response);
                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.serviceSwitch = false;
                    this.serviceStatus = response.data.auto_like_status;
                    this.serviceStatusLabel = response.data.status_labels.auto_like;
                }
            },

            /**
             * 自動いいねサービスを稼働状態にする
             */
            async runLikeService() {
                const serviceType = 3;
                const data = {type: serviceType, twitter_id: this.twitter_id};
                const response = await axios.post('/api/system/running', data);
                if (response.data === 500 || response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notUpdate;
                    this.serviceSwitch = false;
                }
                else{
                    await this.fetchServiceStatus();
                }
            },

            /**
             * 自動いいねサービスを停止状態にする
             */
            async stopLikeService() {
                const serviceType = 3;
                const data = {type: serviceType, twitter_id: this.twitter_id};
                const response = await axios.post('/api/system/stop', data);
                if (response.data === 500 || response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notUpdate;
                    this.serviceSwitch = false;
                }
                else{
                    await this.fetchServiceStatus();
                }
            },
            /**
             * localstorageから現在のページを保存する
             */
            getCurrentPage() {
                localStorage.setItem('page', this.page);
            },
            /**
             * localstorageから現在使用しているtwitter_userのidを取得する
             */
            async getCurrentTwitterId() {
                const storage = JSON.parse(localStorage.getItem('loginTwitterAccount'));
                this.twitter_id = storage.id;
                // console.log(this.twitter_id);
            }
        },
        async created() {
            await this.getCurrentPage();
            await this.getCurrentTwitterId();
            await this.fetchLikes();
            await this.fetchKeywords();
            await this.fetchServiceStatus();
        }
    }
</script>