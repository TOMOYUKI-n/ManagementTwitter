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
        <div>
            <p class="p-table__caption__text">※アンフォロー機能はフォロワーが5000人以内になると、自動的に停止します。</p>
            <div class="p-table__img--wrap">
                <img class="p-table__img--small" :src="'/images/tasks.svg'">
            </div>
        </div>

        <section class="p-modal p-modal--opened" v-show="serviceSwitch">
            <div class="p-modal__contents">
                <p class="p-form__delete">自動化サービスを利用しますか？</p>
                <div class="p-form__delete__wrap">
                    <div type="submit" class="c-button p-form__half-btn width__three" @click="serviceSwitch = false">
                        <i class="fas fa-times m__r2"></i>
                        <div>キャンセル</div>
                    </div>
                    <div v-show="showRunButton" type="submit" class="c-button p-status__active p-form__half-btn width__three" @click="runUnFollowService">
                        <i class="fas fa-check m__r2"></i>
                        <div>開始する</div>
                    </div>
                    <div v-show="showStopButton" type="submit" class="c-button p-status__sleep p-form__half-btn width__three" @click="stopUnFollowService">
                        <i class="fas fa-check m__r2"></i>
                        <div>停止する</div>
                    </div>
                </div>
            </div>
        </section>

    </div>
</template>

<script>
    import { message } from '../message';
    import axios from "axios";
    export default {
        data() {
            return {
                page: 3,
                twitter_id: 0,
                errorFlg: false,
                messageText: '',
                serviceSwitch: false,
                serviceStatus: null,
                serviceStatusLabel: null,
            }
        },
        computed: {
            showRunButton() {
                return this.serviceStatus === 1 || this.serviceStatus === 3
            },
            showStopButton() {
                return this.serviceStatus === 2 || this.serviceStatus === 3
            }
        },
        methods: {
            /**
             * APIを使用して自動アンフォローのステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get(`/api/system/status/${this.twitter_id}`);
                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.serviceSwitch = false;
                    this.serviceStatus = response.data.auto_unfollow_status;
                    this.serviceStatusLabel = response.data.status_labels.auto_unfollow;
                }
            },
            /**
             * APIを使用して自動アンフォローを実行状態にする
             */
            async runUnFollowService() {
                const serviceType = 2;
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
             * APIを使用して自動アンフォローを停止状態にする
             */
            async stopUnFollowService() {
                const serviceType = 2;
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
            setCurrentPage() {
                localStorage.setItem('page', this.page);
            },
            /**
             * localstorageから現在使用しているtwitter_userのidを取得する
             */
            async getCurrentTwitterId() {
                const storage = JSON.parse(localStorage.getItem('loginTwitterAccount'));
                this.twitter_id = storage.id;
            }
        },
        async created() {
            await this.getCurrentTwitterId();
            await this.setCurrentPage();
            await this.fetchServiceStatus();
        }
    }
</script>
