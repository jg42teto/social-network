import {
    USER_DATA_FETCH,
    USER_DATA_NOTIFICATIONS_CHECKED,
    USER_DATA_NOTIFICATIONS_NUMBER,
} from '@/store/interface';
import axios from 'axios';

const state = () => ({
    notifications_number: 0,
});

const getters = {
    [USER_DATA_NOTIFICATIONS_NUMBER](state) {
        return state.notifications_number;
    },
};

const mutations = {
    [USER_DATA_NOTIFICATIONS_NUMBER](state, val) {
        state.notifications_number = val;
    },
};

const actions = {
    [USER_DATA_FETCH]({ commit }) {
        let promise = axios.get('/api/users/data')
        promise.then(({ data }) => {
            commit(USER_DATA_NOTIFICATIONS_NUMBER, data.notifications_number);
        });
        return promise;
    },
    [USER_DATA_NOTIFICATIONS_CHECKED]({ commit }, { last_checked_post_id }) {
        commit(USER_DATA_NOTIFICATIONS_NUMBER, 0);
        return axios.patch('/api/users/notifications-checked', { last_seen_mentioning_post_id: last_checked_post_id });
    },
};

export default {
    state,
    getters,
    actions,
    mutations,
};
