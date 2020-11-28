<template>
<div>


    <div class="p-board__wrap">
        <!-- sp -->
        <div class="p-board__sidebar-sp js_toggle">
            <sidebar-component　@change-page="change" @twitter-id="setId" />
            <footer-component />
        </div>

        <!-- pc -->
        <div class="p-board__sidebar-pc">
            <sidebar-component　@change-page="change" @twitter-id="setId" />
            <footer-component />
        </div>
        <div class="p-board__body">
            <section class="p-board__section">
                <transition-group name="t-dashboard_panel" tag="div" class="">
                    <twitter-account key="account" v-if="page === 1" :twitterAccountId="twitterAccountId" />
                    <panel-follow key="follow" v-if="page === 2" />
                    <panel-unfollow key="unfollow" v-if="page === 3" />
                    <panel-like key="like" v-if="page === 4" />
                    <panel-tweet key="tweet" v-if="page === 5" />
                    <panel-keyword key="keyword" v-if="page === 6" />
                </transition-group>
            </section>
        </div>
    </div>
</div>

</template>

<script>
    export default {
        data() {
            return {
                page: 1,
                isOpen: false,
                twitterAccountId: {}
            }
        },
        methods: {
            /**
             * ページ遷移
             */
            change(page){
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
            }
        },
    }
</script>
<style lang="scss" scoped>

</style>