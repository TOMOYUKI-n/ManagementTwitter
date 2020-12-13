<template>
    <div class="p-panel u-color__bg--white">

        <div class="p-table__title">
            <div class="p-table__sp__title">
                <h2 class="p-table__caption">キーワードリスト</h2>
                <p class="p-table__caption__text">※登録したキーワードは他の自動サービスで「条件」として使用します。</p>
            </div>
            <button class="c-button c-button--add" @click="newModal = !newModal">
                <i class="c-icon__mr-2 c__color--blue fas fa-plus"></i>
                キーワードを追加
            </button>
        </div>
        <p v-show="errorFlg" style="color: red; font-size: 14px; margin-top: 8px;">
            {{ messageText }}
        </p>
        <table class="p-table">
            <tr class="p-table__head">
                <th class="p-table__th p-table__th--keyword">条件タイプ</th>
                <th class="p-table__th p-table__th--keyword">キーワード</th>
                <th class="p-table__th p-table__th--keyword">除外ワード</th>
                <th class="p-table__th p-table__th--keyword">操作</th>
            </tr>
            <tr v-for="(keyword, index) in keywords" :key="index">
                <td class="p-table__td">{{keyword.type}}</td>
                <td class="p-table__td">{{keyword.word}}</td>
                <td class="p-table__td">{{keyword.remove}}</td>
                <td class="p-table__td">
                    <div class="p-table__action">
                        <div class="p-table__btn-wrap">
                            <button class="c-button c-button--twitter p-table__button"
                                    @click="showEditModal(keyword, index)"
                            >
                                <i class="c__color--blue fas fa-pen p-table__test-xs"></i>
                            </button>
                            <button class="c-button c-button--delete p-table__button c-button--delete "
                                    @click="remove(keyword.id, index)"
                            >
                                <i class="fas fa-trash-alt p-table__test-xs"></i>
                            </button>
                        </div>
                    </div>
                </td>
            </tr>

        </table>

        <div class="p-modal__wrapper">
            <section class="p-modal" v-show="newModal">
                <div class="p-modal__contents">
                    <div class="p-modal__cancel u-color__bg--white" @click="newModal = !newModal">
                        <i class="c-icon--gray p-modal__icon fas fa-times"></i>
                    </div>
                    <form class="p-form" @submit.prevent="addKeyword">

                        <p class="p-form__notion">※複数ワードの場合は、「集客 マーケティング」のように半角スペースで区切ってください。</p>
                        <label class="p-form__label" for="add-keyword">条件タイプ</label>
                        <select class="p-form__select" id="add-keyword" v-model="addForm.type">
                            <option value="1">次のワードを含む</option>
                            <option value="2">いずれかのワードを含む</option>
                        </select>
                        <label class="p-form__label" for="keyword">キーワード *必須 最大50文字</label>
                        <input type="text" class="p-form__item" id="keyword"
                               v-model="addForm.word" required maxlength="50">

                        <label class="p-form__label" for="remove_word">除外ワード ※最大50文字</label>
                        <input type="text" class="p-form__item" id="remove_word"
                               v-model="addForm.remove" maxlength="50">

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
                    <form class="p-form" @submit.prevent="editKeyword">


                        <label class="p-form__label" for="edit-keyword">条件タイプ</label>
                        <select class="p-form__select" id="edit-keyword" v-model="editForm.type">
                            <option value="1">次のワードを含む</option>
                            <option value="2">いずれかのワードを含む</option>
                        </select>
                        <label class="p-form__label" for="edit-keyword">キーワード ※必須</label>
                        <input type="text" class="p-form__item" id="edit-keyword"
                               v-model="editForm.word" required maxlength="50">

                        <label class="p-form__label" for="edit-remove_keyword">除外ワード</label>
                        <input type="text" class="p-form__item" id="edit-remove_keyword"
                               v-model="editForm.remove" maxlength="50">
                        <p class="p-form__notion">※複数ワードの場合は、「集客 マーケティング」のように半角スペースで区切ってください。</p>
                        <div class="p-form__button">
                            <button type="submit" class="c-button c-button--sp c-button--twitter c-button__form">変更</button>
                        </div>
                    </form>
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
                        <div type="submit" class="p-button__delete  p-form__half-btn width__three" @click="removeKeywords">
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
    import { filterWords } from "../repository"
    import axios from "axios";
    import { message } from '../message';
    export default {
        data() {
            return {
                page: 6,
                keywords: [],
                newModal: false,
                editModal: false,
                editIndex: null,
                errorFlg: false,
                messageText: '',
                deleteOn: false,
                deleteIndex: 0,
                deleteItem: [],
                addForm: {
                    type: 1,
                    word: '',
                    remove: ''
                },
                editForm: {
                    id: null,
                    type: '',
                    word: '',
                    remove: '',
                },
            }
        },
        methods: {
            /**
             * キーワード一覧を取得
             */
            async fetchKeywords() {
                const response = await axios.get('/api/keyword');
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.keywords = response.data;
                }
            },
            /**
             * 新規キーワードを登録
             */
            async addKeyword() {
                console.log("this.addForm");
                console.log(this.addForm);
                const response = await axios.post('/api/keyword', this.addForm);
                if (response.status !== 200 || response.data === 500) {
                    console.log("error====");
                    console.log(response);
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
                else {
                    this.newModal = false;
                    console.log("success 1");
                    this.resetAddForm();
                    // 一覧を更新
                    console.log("success 2");
                    this.fetchKeywords();
                    console.log("success 3");
                }
            },
            /**
             * 編集フォームモーダルの表示を行って、値を入力しておく
             */
            showEditModal(keyword, index) {
                this.editModal = true;
                this.editForm.id = keyword.id;
                this.editForm.type = keyword.type;
                this.editForm.word = keyword.word;
                this.editForm.remove = keyword.remove;
                this.editIndex = index;
            },
            /**
             * APIを利用してフィルターキーワードの変更を行う
             */
            async editKeyword() {
                const response = await axios.put(`/api/keyword/${this.editForm.id}`, this.editForm);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.editModal = false;
                }
                if (response.data === 404) {
                    this.errorFlg = true;
                    this.messageText = message.notAllowedToChangeKeyword;
                    this.editModal = false;
                }
                else {
                    // 一覧を更新
                    await this.fetchKeywords();
                    this.resetEditForm();
                    this.errorFlg = false;
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
             * APIを利用してフィルターキーワードの削除を行う
             */
            async removeKeywords() {
                const response = await axios.delete(`/api/keyword/${this.deleteItem}`);
                console.log(response);
                if (response.status !== 200 || response.data === 500) {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                    this.deleteOn = false;
                }
                else {
                    this.deleteOn = false;
                    await this.fetchKeywords();
                }
            },
            /**
             * 登録フォームの入力欄を空にする
             */
            resetAddForm() {
                this.addForm.type = 1;
                this.addForm.word = '';
                this.addForm.remove = '';
            },
            /**
             * 編集フォームの入力欄を空にする
             */
            resetEditForm() {
                this.editModal = false;
                this.editForm.id = null;
                this.editForm.type = '';
                this.editForm.word = '';
                this.editForm.remove = '';
                this.editIndex = null;
            },
            /**
             * localstorageから現在のページを保存する
             */
            setCurrentPage() {
                localStorage.setItem('page', this.page);
            }
        },
        async created() {
            await this.fetchKeywords();
            await this.setCurrentPage();
        },
    }
</script>