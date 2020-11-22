<template>
    <div class="p-panel u-color__bg--white">

        <div class="p-status">
            <p v-show="showRunButton" class="p-status__show p-status__sleep" style="background-color: #3335;">{{serviceStatusLabel}}</p>
            <p v-show="showStopButton" class="p-status__show p-status__active">{{serviceStatusLabel}}</p>
            <button class="c-button c-button__status--on"
                    @click="runUnfollowService"
                    v-show="showRunButton">
                    <i class="fas fa-power-off c-icon__mr-2"></i>稼働
            </button>
        </div>
        <div v-show="showStopButton">
            <p class="p-table__caption__text">※ 自動アンフォロー機能はフォロワー5000人以内の場合、自動的に停止されます。</p>
            <div class="p-table__img--wrap">
                <img class="p-table__img--small" :src="'/images/working.png'">
            </div>
        </div>

    </div>
</template>

<script>
    import { filterWords, targetAccountList, manegementServiceStatus } from "../repository"

    export default {
        data() {
            return {
                page: 3,
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
                // const response = await axios.get('/api/system/status')
                // if (response.status !== OK) {
                //     this.$store.commit('error/setCode', response.status)
                //     return false
                // }
                // this.serviceStatus = response.data.auto_unfollow_status
                // this.serviceStatusLabel = response.data.status_labels.auto_unfollow
                this.serviceStatus = 2;
                this.serviceStatusLabel = 'サービス稼働中';
            },

            /**
             * APIを使用して自動アンフォローを実行状態にする
             */
            async runUnfollowService() {
                // const serviceType = 2
                // const data = {type: serviceType}
                // const response = await axios.post('/api/system/run', data)
                // if (response.status !== OK) {
                //     this.$store.commit('error/setCode', response.status)
                //     return false
                // }
                // this.serviceStatus = response.data.auto_unfollow_status
                // this.serviceStatusLabel = response.data.status_labels.auto_unfollow
                const response = manegementServiceStatus;
                this.serviceStatus = 2;
                this.serviceStatusLabel = 'サービス稼働中';
            },

            /**
             * APIを使用して自動アンフォローを停止状態にする
             */
            async stopUnfollowService() {
                // const serviceType = 2
                // const data = {type: serviceType}
                // const response = await axios.post('/api/system/stop', data)
                // if (response.status !== OK) {
                //     this.$store.commit('error/setCode', response.status)
                //     return false
                // }
                // this.serviceStatus = response.data.auto_unfollow_status
                // this.serviceStatusLabel = response.data.status_labels.auto_unfollow
                this.serviceStatus = 1;
                this.serviceStatusLabel = 'サービス停止中';
            },
            /**
             * localstorageから現在のページを保存する
             */
            getCurrentPage() {
                localStorage.setItem('page', this.page);
            }
        },
        created() {
            this.fetchServiceStatus();
            this.getCurrentPage();
        }
    }
</script>
