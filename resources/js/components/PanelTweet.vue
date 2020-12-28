<template>
    <div class="p-panel u-color__bg--white">

        <div class="p-status">
            <p v-show="showRunButton" class="p-status__show p-status__sleep">{{serviceStatusLabel}}</p>
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
                <h2 class="p-table__caption">自動ツイートリスト</h2>
                <p class="p-table__caption__text">※日時を設定してツイートができます</p>
            </div>
            <button v-show="!nothingAccountFlg" class="c-button c-button--add" @click="newModal = !newModal">
                <i class="c-icon__mr-2 c__color--blue fas fa-plus"></i>
                ツイートを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--tweet">状況</th>
                <th class="p-table__th p-table__th--tweet">tweet</th>
                <th class="p-table__th p-table__th--tweet">予約時刻</th>
                <th class="p-table__th p-table__th--tweet"></th>
            </tr>

            <tr v-for="(tweet, index) in tweets" :key="tweet.id">
                <td class="p-table__td">{{tweet.status_label}}</td>
                <td class="p-table__td">{{tweet.tweet}}</td>
                <td class="p-table__td">{{tweet.jp_format_date}}</td>
                <td class="p-table__td">
                    <div class="p-table__action">
                        <div class="p-table__btn-wrap">
                            <button class="c-button c-button--twitter p-table__button"
                                    v-if="tweet.status === 1"
                                    @click="showEditModal(tweet, index)"
                            >
                                <i class="c__color--blue fas fa-pen p-table__test-xs"></i>
                            </button>
                            <button class="c-button c-button--delete p-table__button c-button--delete "
                                    @click="remove(tweet, index)"
                            >
                                <i class="fas fa-trash-alt p-table__test-xs"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <p v-show="tweetsNum === 0" class="p-panel__nodata">
            データがありません
        </p>
        <p v-show="errorFlg" class="p-panel__error">
            {{ messageText }}
        </p>

        <div class="p-modal__wrapper">
            <section class="p-modal" v-show="newModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="closeModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <p v-show="modalErrorFlg" class="p-panel__error">{{ messageModalText }}</p>
                    <form class="p-form" @submit.prevent="addTweet">
                        
                        <label class="p-form__label" for="add-tweet">
                            ツイート内容 {{addTextCount}}/140 *必須
                        </label>
                        <textarea
                            class="p-form__item p-form__item--textarea"
                            id="add-tweet"
                            rows="5"
                            cols="40"
                            v-model="addForm.tweet"
                            required
                            maxlength="140">
                        </textarea>

                        <label class="p-form__label">予定日時 *必須(5分後以降から可能)</label>
                        <div class="u-display__flex--left">
                            <div class="p-form__date--wrap">
                                <div
                                    class="p-form__label p-form__datetime--label"    
                                >投稿年月日</div>
                                <input
                                    type="date"
                                    class="p-form__date"
                                    :min="getCurrentDays"
                                    v-model="addForm.date"
                                    required
                                >
                            </div>
                            <div class="p-form__date--wrap">
                                <div
                                    class="p-form__label p-form__datetime--label"
                                >投稿時間</div>
                                <input
                                    type="time"
                                    class="p-form__time"
                                    v-model="addForm.time"
                                    required
                                >
                            </div>
                        </div>
                        <div class="p-form__button p-form__btn--margin">
                            <button type="submit" class="c-button c-button--sp c-button--add--tweet c-button__form">追加</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal p-modal--opened" v-show="editModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="editModal = !editModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <p v-show="modalErrorFlg" class="p-panel__error">{{ messageModalText }}</p>
                    <form class="p-form" @submit.prevent="editTweet">
                        
                        <label class="p-form__label" for="edit-tweet">
                            ツイート内容 {{editTextCount}}/140 *必須
                        </label>
                        <textarea
                            class="p-form__item p-form__item--textarea"
                            id="edit-tweet"
                            rows="5"
                            cols="40"
                            v-model="editForm.tweet"
                            required
                            maxlength="140"
                        >
                        </textarea>

                        <label class="p-form__label">予定日時 *必須(5分後以降から可能)</label>
                        <div class="u-display__flex--left">

                            <label class="p-form__label" for="edit-date">
                                投稿年月日
                            </label>
                            <input
                                id="edit-date"
                                type="date"
                                class="p-form__date"
                                :min="getCurrentDays"
                                value="getCurrentDays"
                                v-model="editForm.date"
                                required
                            >
                            <label class="p-form__label" for="edit-time">
                                投稿時間
                            </label>
                            <input
                                id="edit-time"
                                type="time"
                                class="p-form__time"
                                v-model="editForm.time"
                                required
                            >
                        </div>
                        <div class="p-form__button p-form__btn--margin">
                            <button type="submit" class="c-button c-button--sp c-button--add--tweet c-button__form">変更</button>
                        </div>
                    </form>
                </div>
            </section>
        </div>

        <section class="p-modal p-modal--opened" v-show="serviceSwitch">
            <div class="p-modal__contents">
                <p class="p-form__delete">自動化サービスを利用しますか？</p>
                <div class="p-form__delete__wrap">
                    <div type="submit" class="c-button p-form__half-btn width__three" @click="serviceSwitch = false">
                        <i class="fas fa-times m__r2"></i>
                        <div>キャンセル</div>
                    </div>
                    <div v-show="showRunButton" type="submit" class="c-button p-status__active p-form__half-btn width__three" @click="runTweetService">
                        <i class="fas fa-check m__r2"></i>
                        <div>開始する</div>
                    </div>
                    <div v-show="showStopButton" type="submit" class="c-button p-status__sleep p-form__half-btn width__three" @click="stopTweetService">
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
                    <div type="submit" class="p-button__delete  p-form__half-btn width__three" @click="removeTweet">
                        <i class="fas fa-check m__r2"></i>
                        <div>削除</div>
                    </div>
                </div>
            </div>
        </section>

        <div class="c-button--add--wrap">
            <button class="c-button--add--sp" @click="newModal = !newModal">
                <i class="fas fa-plus"></i>
            </button>
        </div>
    </div>
</template>

<script>
    import { message } from '../message';
    import axios from "axios";
    export default {
        data() {
            return {
                page: 5,
                twitter_id: 0,
                errorFlg: false,
                modalErrorFlg: false,
                nothingAccountFlg: false,
                messageText: '',
                messageModalText: '',
                serviceSwitch: false,
                deleteOn: false,
                deleteIndex: 0,
                deleteItem: [],
                tweets: [],
                tweetsNum: 0,
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                addForm: {
                    tweet: '',
                    date: this.formatter(new Date()),
                    time: this.setAfterFiveTime(),
                },
                editForm: {
                    tweet: '',
                    date: '',
                    time: '',
                }
            }
        },
        computed: {
            /**
             * 新規登録時 ツイートの文字数をカウントする
             */
            addTextCount: function () {
                return this.addForm.tweet.length;
            },
            /**
             * 編集時 ツイートの文字数をカウントする
             */
            editTextCount: function () {
                return this.editForm.tweet.length;
            },
            /**
             * 現在の日付をYYYY-MM-DD形式で取得する
             */
            getCurrentDays: function () {
                const date = new Date();
                const year = date.getFullYear();
                const month = ("00" + (date.getMonth() + 1)).slice(-2);
                const day = ("00" + date.getDate()).slice(-2);
                return [year, month, day].join("-");
            },
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3;
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3;
            },
        },
        methods: {
            /**
             * APIを使用して登録した自動ツイート一覧を取得する
             */
            async fetchTweets() {
                const response = await axios.get(`/api/tweet/list/${this.twitter_id}`);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.tweetsNum = response.data[1];
                    this.tweets = response.data[0];
                }
            },
            /**
             * 5分後の時刻でないと入力できないように制限
             */
            async validateTime(args) {
                const timer = args.date + ' ' + args.time;
                console.log(timer);
                const time = new Date(timer);
                console.log(time);
                const info = Date.parse(time);
                console.log(info);

                // Date形式で5分後の時刻を取得
                const afterFiveTime = new Date(+new Date() + (5 * 60 * 1000));
                const afterInfo = Date.parse(afterFiveTime);

                // 5分以上間を開けているか判定
                return info > afterInfo ? true:false;
            },
            /**
             * APIを使用して自動ツイートを新規登録する
             */
            async addTweet() {
                this.modalErrorFlg = false;
                this.messageModalText = '';
                // 5分後の制限
                const checked = await this.validateTime(this.addForm);
                if (checked) {
                    const response = await axios.post(`/api/tweet/${this.twitter_id}`, this.addForm);
                    if (response.status !== 200 || response.data === 500) {
                        this.newModal = false;
                        this.errorFlg = true;
                        this.messageText = message.notUpdate;
                    }
                    if (response.data === 200) {
                        this.newModal = false;
                        // 再描画
                        this.resetAddForm();
                        await this.fetchTweets();
                    }
                }
                else {
                    this.modalErrorFlg = true;
                    this.messageModalText = message.noFiveMinutesTimer;
                }
            },

            /**
             * APIを使用して自動ツイートを編集する
             */
            async editTweet() {
                this.modalErrorFlg = false;
                this.messageModalText = '';
                const checked = await this.validateTime(this.editForm);
                if (checked) {
                    const response = await axios.post(`/api/tweet/edit/${this.twitter_id}`, this.editForm);
                    if (response.status !== 200 || response.data === 500) {
                        this.errorFlg = true;
                        this.messageText = message.notGetData;
                        this.resetEditForm();
                    }
                    if (response.data === 200) {
                        // 再描画
                        this.resetEditForm();
                        await this.fetchTweets();
                    }
                }
                else {
                    this.modalErrorFlg = true;
                    this.messageModalText = message.noFiveMinutesTimer;
                }
            },
            /**
             * 自動ツイート編集用のモーダルフォームを表示する
             * 表示した際に、自動ツイートのデータをフォームに入力しておく
             */
            showEditModal(tweet, index) {
                this.editForm.id = tweet.id;
                this.editForm.tweet = tweet.tweet;
                this.editForm.date = this.formatter(tweet.format_date);
                this.editForm.time = this.getHHMM(tweet.format_date);
                this.editIndex = index;
                this.editModal = true;
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
             * APIを使用して自動ツイートを削除する
             */
            async removeTweet() {
                const response = await axios.delete(`/api/tweet/${this.deleteItem.id}`);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.deleteOn = false;
                }
                if (response.data === 200) {
                    this.deleteOn = false;
                    this.tweets.splice(this.deleteIndex, 1);
                    // 再描画
                    await this.fetchTweets();
                }
            },
            /**
             * Datetime型をYYYY-MM-DD形式に変換する
             */
            formatter(format_date) {
                const date = new Date(format_date);
                const year = date.getFullYear();
                const month = ("00" + (date.getMonth() + 1)).slice(-2);
                const day = ("00" + date.getDate()).slice(-2);
                return [year, month, day].join("-");
            },

            /**
             * Datetime型をHH:MMの形式に変換する
             */
            getHHMM(format_date) {
                const date = new Date(format_date)
                const hours = ("00" + date.getHours()).slice(-2)
                const minutes = ("00" + date.getMinutes()).slice(-2)
                return [hours, minutes].join(":")
            },
            /**
             * 初期値に5分後をセットする
             */
            setAfterFiveTime() {
                const afterFiveTime = new Date(+new Date() + (6 * 60 * 1000));
                const hours = ("00" + afterFiveTime.getHours()).slice(-2);
                const minutes = ("00" + afterFiveTime.getMinutes()).slice(-2);
                return [hours, minutes].join(":");
            },
            /**
             * 新規登録フォームのリセットを行う
             */
            resetAddForm() {
                this.addForm.tweet = '';
                this.addForm.date = this.formatter(new Date());
                this.addForm.time = this.setAfterFiveTime();
            },
            /**
             * 編集フォームのリセットを行う
             */
            resetEditForm() {
                this.editModal = false;
                this.editForm.id = null;
                this.editForm.tweet = '';
                this.editForm.date = this.formatter(new Date());
                this.editForm.time = this.getHHMM(new Date());
                this.editIndex = null;
            },

            /**
             * APIを使用して自動ツイートのサービスステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get(`/api/system/status/${this.twitter_id}`);
                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.serviceSwitch = false;
                    this.serviceStatus = response.data.auto_tweet_status;
                    this.serviceStatusLabel = response.data.status_labels.auto_tweet;
                }
            },

            /**
             * 自動ツイートサービスを稼働状態に変更する
             */
            async runTweetService() {
                const serviceType = 4;
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
             * 自動ツイートサービスを停止状態にする
             */
            async stopTweetService() {
                const serviceType = 4;
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
             * フォームのエラーメッセージをクリアする
             */
            clearErrors() {
                this.addErrors = null
                this.editErrors = null
            },
            /**
             * localstorageから現在のページを保存する
             */
            setCurrentPage() {
                localStorage.setItem('page', this.page);
            },
            /**
             * localstorageから現在使用しているtwitter_userのidを取得する
             */
            async getCurrentTwitterId() {
                const storage = JSON.parse(localStorage.getItem('loginTwitterAccount'));
                if(storage){
                    this.twitter_id = storage.id;
                }else{
                    this.errorFlg = true;
                    this.nothingAccountFlg = true;
                    this.messageText = message.needSelectAccount;
                }
            },
            closeModal() {
                this.newModal = false;
                this.editModal = false;
                this.modalErrorFlg = false;
                this.resetAddForm();
            }
        },
        async created() {
            await this.setCurrentPage();
            await this.getCurrentTwitterId();
            await this.fetchServiceStatus();
            await this.fetchTweets();
        },
    }

</script>
<style lang="scss" scoped>
</style>