import axios from "axios";
import Cookie from "js-cookie";
import {
    AUTH_REQUEST,
    AUTH_LOGOUT,
    AUTH_AUTHENTICATED,
    AUTH_USER_ID,
    AUTH_USER_USERNAME,
    AUTH_ADMIN,
    AUTH_SUPERADMIN,
    AUTH_USER_ROLE,
} from '@/store/interface'
import router from '@/router'

const state = () => ({
    user_id: parseInt(Cookie.get("user_id")),
    username: Cookie.get("username"),
    role: Cookie.get("role"),
});

const getters = {
    [AUTH_AUTHENTICATED](state) {
        return !!state.user_id;
    },
    [AUTH_USER_ID](state) {
        return state.user_id
    },
    [AUTH_USER_USERNAME](state) {
        return state.username
    },
    [AUTH_ADMIN](state) {
        return state.role == "admin" || state.role == "superadmin";
    },
    [AUTH_SUPERADMIN](state) {
        return state.role == "superadmin";
    },

};

const actions = {
    [AUTH_REQUEST]({ commit }, { data, auth_type }) {
        return new Promise((resolve, reject) =>
            axios.get('/sanctum/csrf-cookie')
                .then(() => axios.post(`/api/auth/${auth_type}`, data))
                .then((res) => {
                    if (auth_type == 'login' || auth_type == 'register') {
                        let id = parseInt(res.data.user.id);
                        let username = res.data.user.username;
                        let role = 'user';
                        switch (res.data.user.admin) {
                            case 1: role = 'admin'; break;
                            case 2: role = 'superadmin'; break;
                            default: role = 'user'; break;
                        }
                        Cookie.set("user_id", id);
                        Cookie.set("username", username);
                        Cookie.set("role", role);
                        commit(AUTH_USER_ID, id);
                        commit(AUTH_USER_USERNAME, username);
                        commit(AUTH_USER_ROLE, role);
                    }
                    resolve(res);
                })
                .catch(err => reject(err)))
    },
    [AUTH_LOGOUT]({ commit }) {
        let promise = axios.post('/api/auth/logout');
        promise.finally(() => {
            Cookie.remove("user_id");
            Cookie.remove("username");
            Cookie.remove("role");
            router.push({ name: "login" });
            this.reset();
        })
        return promise;
    },
};

const mutations = {
    [AUTH_USER_ID](state, val) {
        state.user_id = val
    },
    [AUTH_USER_USERNAME](state, val) {
        state.username = val
    },
    [AUTH_USER_ROLE](state, val) {
        state.role = val;
    },
};

export default {
    state,
    getters,
    actions,
    mutations
};