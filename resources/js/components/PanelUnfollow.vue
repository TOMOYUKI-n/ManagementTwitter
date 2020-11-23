<template>
    <div class="p-panel u-color__bg--white">

        <div class="p-status">
            <p v-show="showRunButton" class="p-status__show p-status__sleep">{{serviceStatusLabel}}</p>
            <p v-show="showStopButton" class="p-status__show p-status__active">{{serviceStatusLabel}}</p>
            <!-- <button class="c-button c-button__status--on"
                    @click="runUnfollowService"
                    v-show="showRunButton">
                    <i class="fas fa-power-off c-icon__mr-2"></i>稼働
            </button> -->

        </div>
        <div>
            <p class="p-table__caption__text">※アンフォロー機能はフォロワーが5000人以内になると、自動的に停止します。</p>
            <div class="p-table__img--wrap">
                <img class="p-table__img--small" :src="'/images/working.png'">
            </div>
        </div>

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
                // console.log(response);
                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.serviceStatus = response.data.auto_unfollow_status;
                    this.serviceStatusLabel = response.data.status_labels.auto_unfollow;
                }
            },
            /**
             * APIを使用して自動アンフォローを実行状態にする
             */
            // async runUnFollowService() {
            //     const serviceType = 2;
            //     const data = {type: serviceType, twitter_id: this.twitter_id};
            //     const response = await axios.post('/api/system/running', data);
            //     if (response.data === 500 || response.status !== 200) {
            //         this.errorFlg = true;
            //         this.messageText = message.notUpdate;
            //     }
            //     else{
            //         await this.fetchServiceStatus();
            //     }
            // },
            /**
             * APIを使用して自動アンフォローを停止状態にする
             */
            // async stopUnFollowService() {
            //     const serviceType = 2;
            //     const data = {type: serviceType, twitter_id: this.twitter_id};
            //     const response = await axios.post('/api/system/stop', data);
            //     if (response.data === 500 || response.status !== 200) {
            //         this.errorFlg = true;
            //         this.messageText = message.notUpdate;
            //     }
            //     else{
            //         await this.fetchServiceStatus();
            //     }
            // },
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
            }
        },
        async created() {
            await this.getCurrentTwitterId();
            await this.getCurrentPage();
            await this.fetchServiceStatus();
        }
    }
</script>
