<template>
<div class="p-board__wrap">
    <div  class="p-board__sidebar" >
        <div>
            <div class="p-board__mp-4">
                <div class="p-board__top">@xxkajiya</div>
                <div class="p-board__d-flex p-board__top p-board__space-end">
                    <div class="p-board__mr-2 p-board__text-small">フォロー</div>
                    <div>
                        {{ followNum }}
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
                <div class="p-board__d-flex p-board__top p-board__space-end">
                    <div class="p-board__mr-2 p-board__text-small">フォロワー</div>
                    <div>
                        {{ followerNum }}
                        <small class="p-board__text-small">人</small>
                    </div>
                </div>
            </div>
            <div>
                <div v-for="(i, index) in link" :key="index">
                    <div class="p-board__link p-board__hover" @click="change(index)"
                        :class="[flgId === i.id ? 'is-selected' : '']">
                        <div class="">{{ i.label }}</div>
                    </div>
                </div>
            </div>
        </div>
        <footer class="p-footer">
            <div class="p-footer__info">
                <div class="p-footer__menu">
                    <div class="p-footer__list"><a class="p-footer__href" href="/contact">お問い合わせ </a></div>
                    <div class="p-footer__list"><a class="p-footer__href" href="/term">利用規約 </a></div>
                    <div class="p-footer__list"><a class="p-footer__href" href="/policy">プライバシーポリシー</a></div>
                </div>
            </div>
            <div class="p-footer__copyright">
                <div>©kamitter2020 kamitter.All Rights Reserved</div>
            </div>
        </footer>
    </div>
    <div class="p-board__body">
        <section class="p-board__section">
            <transition-group name="t-dashboard_panel" tag="div">
                <twitter-account key="account" v-if="page === 1" />
                <panel-follow key="follow" v-if="page === 2" />
                <panel-unfollow key="unfollow" v-if="page === 3" />
                <panel-like key="like" v-if="page === 4" />
                <panel-tweet key="tweet" v-if="page === 5" />
                <panel-filter key="filter" v-if="page === 6" />
            </transition-group>
        </section>
    </div>
</div>

</template>

<script>
    import {linkParam} from "../utility";

    export default {
        data() {
            return {
                page: 1,
                followNum: 300,
                followerNum: 400,
                flgId:1,
                link: linkParam,
            }
        },
        methods: {
            change(index){
                this.flgId = index + 1;
                this.page = this.flgId;
            }
        }
    }
</script>
<style lang="scss" scoped>
</style>