<template>

</template>
<script>
export default {
    data() {
        return {
            stopCheck
        }
    },
    methods: {
        /**
         * ユーザーが登録しているTwitterUserのID一覧を取得する
         */
        stopCheck() {
            this.$emit('stopCheck');
        }
    }
}
</script>
