<template>
<div>
        <!-- sp -->
        <div class="p-board__sidebar-sp js_open-sidebar">
            <sidebar-component　@change-page="change" @twitter-id="setId" />
        </div>

        <!-- pc -->
        <div class="p-board__sidebar-pc">
            <sidebar-component　@change-page="change" @twitter-id="setId" />
        </div>
        <div class="p-board__body">
            <section class="p-board__section">
                <transition-group name="t-dashboard_panel" tag="div">
                    <twitter-account key="account" v-if="page === 1" 
                        :twitterAccountId="twitterAccountId"
                        @user-delete="twitterUserDelete"
                    />
                    <panel-follow key="follow" v-if="page === 2" />
                    <panel-unfollow key="unfollow" v-if="page === 3" />
                    <panel-like key="like" v-if="page === 4" />
                    <panel-tweet key="tweet" v-if="page === 5" />
                    <panel-keyword key="keyword" v-if="page === 6" />
                </transition-group>
            </section>
        </div>
</div>

</template>

<script>
    export default {
        data() {
            return {
                page: 1,
                isOpen: false,
                twitterAccountId: {},
            }
        },
        methods: {
            /**
             * ページ遷移
             */
            change(page){
                console.log(page);
                this.page = page;
                this.isOpen = false;
            },
            /**
             * サイドバーの開閉
             */
            openSidebar() {
                this.isOpen = !this.isOpen;
            },
            /**
             * twitter_idを渡す
             */
            setId(account) {
                this.twitterAccountId = account.id;
            },
            /**
             * twitterUserを削除
             * sidebarを更新して、this.loginTwitterUserを空にする
             */
            twitterUserDelete() {
                this.twitterAccountId = 0;
            },
        },
    }
</script>
<style lang="scss" scoped>

</style>