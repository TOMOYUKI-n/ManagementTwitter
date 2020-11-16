
const authActions = {

    /**
     * APIを使ってログインを行う
     */
    async login(context, data) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/login', data)

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', response.data)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        if (response.status === UNPROCESSABLE_ENTRY) {
            context.commit('setLoginErrorMessages', response.data.errors)
        } else {
            context.commit('error/setCode', response.status, {root: true})
        }
    },

    /**
     * APIを使ってログアウトを行う
     */
    async logout(context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/logout')

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', null)
            context.commit('setTwitterUser', null)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    },

    /**
     * APIを使ってユーザーログインチェックする
     * sessionからユーザーIDを取得する
     */
    async currentUser(context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/user')
        const user = response.data || null

        // API成功時
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setUser', user)
            return false
        }

        // API失敗時
        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    },


    /**
     * APIを使ってツイッターユーザーログインチェックする
     * sessionからTwitterUserIdを取得する
     */
    async currentTwitterUser(context) {
        context.commit('setApiStatus', null)
        const response = await axios.get('/api/twitter/id')
        const id = response.data || null

        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setTwitterUser', id)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    },


    /**
     * APIを使ってツイッターユーザーログアウトする
     */
    async twitterUserLogout(context) {
        context.commit('setApiStatus', null)
        const response = await axios.post('/api/twitter/logout')
        if (response.status === OK) {
            context.commit('setApiStatus', true)
            context.commit('setTwitterUser', null)
            return false
        }

        context.commit('setApiStatus', false)
        context.commit('error/setCode', response.status, {root: true})
    }
};

export default authActions;