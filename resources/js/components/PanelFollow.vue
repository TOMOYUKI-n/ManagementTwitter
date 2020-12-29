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
                <h2 class="p-table__caption">対象アカウントリスト</h2>
                <p class="p-table__caption__text">※ここでは登録されたアカウントの "フォロワー" に対して、自動フォローを行えます。</p>
            </div>
            <button v-show="!nothingAccountFlg" class="c-button c-button--add" @click="newModal = ! newModal">
                <i class="c-icon__mr-2 c__color--blue fas fa-plus"></i>
                対象アカウントを追加
            </button>
        </div>

        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--follow">ステータス</th>
                <th class="p-table__th p-table__th--follow">ターゲット</th>
                <th class="p-table__th p-table__th--follow">条件</th>
                <th class="p-table__th p-table__th--follow"></th>
            </tr>

            <tr v-for="(followTarget, index) in followTargets" :key="index">
                <td class="p-table__td">{{followTarget.status_label}}</td>
                <td class="p-table__td">@{{followTarget.account_user_name}}</td>
                <td class="p-table__td">{{followTarget.keyword.merged_word}}</td>
                <td class="p-table__td">
                    <div class="p-table__action">
                        <div class="p-table__btn-wrap">
                            <button v-if="followTarget.status_label !== 'リスト作成済'" class="c-button c-button--twitter p-table__button"
                                    @click="showEditModal(followTarget, index)"
                            >
                                <i class="c__color--blue fas fa-pen p-table__test-xs"></i>
                            </button>
                            <button class="c-button c-button--delete p-table__button c-button--delete "
                                    @click="remove(followTarget, index)"
                            >
                                <i class="fas fa-trash-alt p-table__test-xs"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>
        </table>
        <p v-show="followTargets.length === 0" class="p-table__no-data">
            データがありません
        </p>
        <p v-show="errorFlg" class="p-table__error--message">
            {{ messageText }}
        </p>

        <div class="p-modal__wrapper">
            <section class="p-modal p-modal--opened" v-show="newModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="newModal = !newModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <form class="p-form" @submit.prevent="addFollowTarget">

                        <p class="p-form__notion">※条件のキーワードは、「キーワード登録」から登録することができます。</p>
                        <label class="p-form__label" for="add-target">ターゲット名(@は不要) *必須</label>
                        <p v-show="modalErrorFlg" style="color: red; font-size: 13px;">
                            {{ messageModalText }}
                        </p>
                        <input type="text" class="p-form__item" id="add-account"
                               v-model="addForm.account_user_name" required maxlength="15" placeholder="例) kamitter_1234">

                        <label class="p-form__label" for="add-account_id">フォロー条件の選択 *必須</label>
                        <select class="p-form__select" id="add-account_id"
                                v-model="addForm.keyword_id"
                                required
                        >
                            <option v-for="keyword in keywords" :key="keyword.id" :value="keyword.id">{{keyword.word}}</option>
                            <optgroup></optgroup>
                        </select>

                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--sp c-button--twitter c-button__form">追加</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal" v-show="editModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="editModal = !editModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <form class="p-form" @submit.prevent="editFollowTarget">

                        <p class="p-form__notion">※キーワードは、「キーワード登録」から登録してください。</p>

                        <label class="p-form__label" for="account">ターゲット名(@は不要) *必須</label>
                        <p v-show="modalErrorFlg" style="color: red; font-size: 13px;">
                            {{ messageModalText }}
                        </p>
                        <input type="text" class="p-form__item" id="account"
                               v-model="editForm.account_user_name" required maxlength="15" placeholder="例) kamitter_1234">

                        <label class="p-form__label" for="keyword_id">フォロー条件の選択 *必須</label>
                        <select class="p-form__select" id="keyword_id"
                                v-model="editForm.keyword_id"
                                required
                        >
                            <option v-for="keyword in keywords" :key="keyword.id" :value="keyword.id">{{keyword.word}}</option>
                        </select>

                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--sp c-button--twitter c-button__form" >編集</button>
                        </div>
                    </form>
                </div>
            </section>

            <section class="p-modal p-modal--opened" v-show="serviceSwitch">
                <div class="p-modal__contents">
                    <p class="p-form__delete">自動化サービスを利用しますか？</p>
                    <p v-show="modalErrorFlg" style="color: red; font-size: 13px; text-align: center;">
                        {{ messageModalText }}
                    </p>
                    <div class="p-form__delete__wrap">
                        <div type="submit" class="c-button p-form__half-btn width__three" @click="serviceSwitch = false">
                            <i class="fas fa-times m__r2"></i>
                            <div>キャンセル</div>
                        </div>
                        <div v-show="showRunButton" type="submit" class="c-button p-status__active p-form__half-btn width__three" @click="runFollowService">
                            <i class="fas fa-check m__r2"></i>
                            <div>開始する</div>
                        </div>
                        <div v-show="showStopButton" type="submit" class="c-button p-status__sleep p-form__half-btn width__three" @click="stopFollowService">
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
                        <div type="submit" class="p-button__delete  p-form__half-btn width__three" @click="removeFollowTarget">
                            <i class="fas fa-check m__r2"></i>
                            <div>削除</div>
                        </div>
                    </div>
                </div>
            </section>

        </div>

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
                page: 2,
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
                followTargets: [],
                keywords: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                serviceStatus: null,
                serviceStatusLabel: null,
                addForm: {
                    account_user_name: null,
                    keyword_id: null,
                },
                editForm: {
                    id: 0,
                    account_user_name: '',
                    keyword_id: 0,
                },
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
             * 登録したフォローターゲット一覧を取得する
             */
            async fetchFollowTargets() {
                const response = await axios.get(`/api/follow/list/${this.twitter_id}`);

                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.followTargets = response.data;
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
             * 新規登録時のバリデーション
             */
            validate(name) {
                // 初期化
                this.modalErrorFlg = false;
                this.messageModalText = '';
                
                if(name.indexOf('@') !== -1 || name.indexOf('＠') !== -1) {
                    return true;
                } else {
                    return false;
                }
            },
            /**
             * 新規フォローターゲットを追加する
             */
            async addFollowTarget() {
                // @が文字列に含まれている場合エラーにする
                if(this.validate(this.addForm.account_user_name)) {
                    this.modalErrorFlg = true;
                    this.messageModalText = message.noAtMark;
                } else {
                    // 問題なければAPIで追加
                    const response = await axios.post(`/api/follow/${this.twitter_id}`, this.addForm);
                    if (response.status !== 200) {
                        this.errorFlg = true;
                        this.messageText = message.notUpdate;
                    }
                    if (response.data === 200) {
                        this.resetAddForm();
                        this.newModal = false;
                        // 再描画
                        await this.fetchFollowTargets();
                        await this.fetchKeywords();
                    }
                }
            },
            /**
             * 編集用のモーダルフォームを表示する
             * 表示の際にフォローターゲットのデータを入力しておく
             */
            showEditModal(followTarget, index) {
                this.editModal = true;
                this.editForm.id = followTarget.id;
                this.editForm.account_user_name = followTarget.account_user_name;
                this.editForm.keyword_id = followTarget.keyword_id;
                this.editIndex = index;
            },
            /**
             * フォーローターゲットを編集する
             */
            async editFollowTarget() {
                if(this.validate(this.editForm.account_user_name)) {
                    this.modalErrorFlg = true;
                    this.messageModalText = message.noAtMark;
                } else {
                    const response = await axios.put(`/api/follow/edit`, this.editForm);
                    if (response.status !== 200 || response.data === 500) {
                        this.errorFlg = true;
                        this.messageText = message.notGetData;
                    }
                    if (response.data === 200) {
                        // 再描画
                        await this.fetchFollowTargets();
                        await this.fetchKeywords();
                    }
                    this.resetEditForm();
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
             * フォローターゲットを削除する
             */
            async removeFollowTarget() {
                const response = await axios.post(`/api/follow/delete/${this.deleteItem.id}`, this.deleteItem);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.deleteOn = false;
                }
                if (response.data === 200) {
                    this.deleteOn = false;
                    this.followTargets.splice(this.deleteIndex, 1);
                    // 再描画
                    await this.fetchFollowTargets();
                    await this.fetchKeywords();
                }
            },
            /**
             * フォームを初期化
             */
            resetAddForm() {
                this.addForm.account_user_name = null
                this.addForm.keyword_id = null
            },
            resetEditForm() {
                this.editModal = null
                this.editForm.id = null
                this.editForm.account_user_name = null
                this.editForm.keyword_id = null
                this.editIndex = null
            },
            /**
             * 自動フォロー機能のサービスステータスを取得する
             */
            async fetchServiceStatus() {
                const response = await axios.get(`/api/system/status/${this.twitter_id}`);

                if (response.status !== 200) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.serviceSwitch = false;
                    this.serviceStatus = response.data.auto_follow_status;
                    this.serviceStatusLabel = response.data.status_labels.auto_follow;
                }
            },
            /**
             * 自動フォロー機能を稼働状態にする
             */
            async runFollowService() {
                if (this.followTargets.length === 0) {
                    this.modalErrorFlg = true;
                    this.messageModalText = message.noTargetAccount;
                } else {
                    const serviceType = 1;
                    const data = {type: serviceType, twitter_id: this.twitter_id};
                    const response = await axios.post('/api/system/running', data);
                    if (response.data === 500 || response.status !== 200) {
                        this.errorFlg = true;
                        this.messageStatusText = message.notUpdate;
                        this.serviceSwitch = false;
                    }
                    else{
                        await this.fetchServiceStatus();
                    }
                }
            },
            /**
             * 自動フォロー機能を停止状態にする
             */
            async stopFollowService() {
                const serviceType = 1;
                const data = {type: serviceType, twitter_id: this.twitter_id};
                const response = await axios.post('/api/system/stop', data);
                if (response.data === 500 || response.status !== 200) {
                    this.errorFlg = true;
                    this.messageStatusText = message.notUpdate;
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
                if(storage){
                    this.twitter_id = storage.id;
                }else{
                    this.errorFlg = true;
                    this.nothingAccountFlg = true;
                    this.messageText = message.needSelectAccount;
                }
            }
        },
        async created() {
            await this.setCurrentPage();
            await this.getCurrentTwitterId();
            await this.fetchFollowTargets()
            await this.fetchKeywords()
            await this.fetchServiceStatus();
        },
    }
</script>
<style lang="scss" scoped>
.running{
    border-color: #5cb85c;
    background: #5cb85c;
}
.stopping{
    border-color: #EF5350;
    background: #EF5350;
}

</style>