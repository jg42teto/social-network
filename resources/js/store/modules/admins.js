import {
    ADMIN_ADMINS_FETCH,
    ADMIN_ADMIN_TOGGLE,
    ADMIN_USER_FETCH,
    ADMIN_USER_BLOCK_TOGGLE,
} from "@/store/interface";
import User from '@/models/User';

const state = () => ({});

const getters = {};

const mutations = {};

const actions = {
    [ADMIN_ADMINS_FETCH](context) {
        let promise = axios.get('api/admin/admins');
        promise.then(({ data }) => {
            User.insertOrUpdate({ data });
        });
        return promise;
    },
    [ADMIN_ADMIN_TOGGLE](context, id) {
        let promise = axios.patch(`api/admin/admins/${id}/toggle-admin`);
        promise.then(({ data }) => {
            User.update({ data });
        });
        return promise;
    },
    [ADMIN_USER_FETCH](context, id) {
        let promise = axios.get(`api/admin/users/${id}`);
        promise.then(({ data }) => {
            User.insertOrUpdate({ data });
        });
        return promise;
    },
    [ADMIN_USER_BLOCK_TOGGLE](context, id) {
        let promise = axios.patch(`api/admin/users/${id}/toggle-block`);
        promise.then(({ data }) => {
            User.update({ data });
        });
        return promise;
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
