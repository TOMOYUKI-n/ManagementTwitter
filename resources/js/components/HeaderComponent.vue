<template>
<div>
    <header class="l-navbar__main-wrap" style="z-index: 1000;">
        <div class="l-navbar__align">
            <div class="l-navbar__sp__d-flex">
                <div class="l-navbar__icon">
                    <i class="fas fa-bars" @click="open"></i>
                </div>
                <div class="logo">
                    <h1><a class="l-navbar__text" href="/">kamitter</a></h1>
                </div>
            </div>
        </div>
    </header>
</div>
</template>

<script>
    export default {
        methods: {
            /**
             * サイドバーの開閉
             */
            open() {
                this.$emit('isOpen');
            }
        }
    }
</script>
<style lang="scss" scoped>
</style>