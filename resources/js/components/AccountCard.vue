<template>
    <li class="c-card p-twitter__card u-color__bg--white" @click="setTwitterId"
        :class="[selectId === twitter_id? 'isSelected': '']">
        <div class="p-twitter__width-80">
            <figure>
                <img class="p-twitter__img" :src="thumbnail" alt="">
            </figure>
            <div class="p-twitter__ids">
                <p class="p-twitter__text__bold">{{name}}</p>
                <p class="p-twitter__ids">@{{screenName}}</p>
            </div>
        </div>
        <div>
            <div class="" @click.stop="changeDeleteFlg">
                <i class="p-button__trash fas fa-trash-alt"></i>
            </div>
        </div>
    </li>
</template>

<script>
    import { message } from '../message';
    import axios from "axios";
    export default {
        props: {
            selectId: 0,
            item: {
                type: Object,
                required: true
            },
            index: {
                type: Number,
                required: true
            },
            deleteActionFlg: false
        },
        data() {
            return {
                del: 1,
                twitter_id: 0,
                screenName: '',
                name: '',
                thumbnail: '',
            }
        },
        methods: {
            /**
             * TwitterUserのユーザ情報を1件取得する
             */
            async fetchTwitterUser() {
                const response = await axios.get('/api/twitter/users/' + this.item.id);
                if (response.status === 200) {
                    this.twitter_id = response.data.twitter_id;
                    this.screenName = response.data.screen_name;
                    this.name = response.data.name;
                    this.thumbnail = response.data.thumbnail;
                }
                else {
                    this.errorFlg = true;
                    this.messageText = message.notGetData;
                }
            },
            /**
             * 使用するユーザーが選択された時、localstorageにtwitterIdを保存 => ダッシュボードに遷移
             */
            async setTwitterId() {
                await localStorage.setItem('loginTwitterAccount',JSON.stringify(this.item));
                this.$emit('selectAccount');
                location.href = "/dashboard";
            },
            /**
             * 削除前の確認モーダルを表示する
             */
            changeDeleteFlg() {
                this.$emit('delUser', { index: this.index, item_id: this.item.id });
            },
        },
        async created() {
            await this.fetchTwitterUser();
        }
    }
</script>
<style lang="scss" scoped>
.isSelected{
    background: rgba(39, 144, 248, 0.2);
}
</style>
