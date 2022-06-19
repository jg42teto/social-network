import {
    USER_FETCH,
    USER_EDIT,
    USER_UPLOAD_AVATAR,
    USER_SEARCH,
    USER_FOLLOW_TOGGLE,
    AUTH_USER_ID,
} from '@/store/interface';
import User from '@/models/User';
import Profile from '@/models/Profile';
import axios from 'axios';

const state = () => ({});

const getters = {};

const mutations = {};

const actions = {
    [USER_FETCH](context, username) {
        let promise = axios.get(`/api/users?username=${username}`)
        promise.then(({ data }) => {
            User.insertOrUpdate({
                data
            })
        })
        return promise;
    },
    [USER_SEARCH](context, term) {
        return axios.get(`/api/users/search?term=${term}`)
    },
    [USER_EDIT](context, data) {
        let promise = axios.put('/api/users', data);
        promise.then(({ data }) => {
            User.update({
                data
            })
        })
        return promise;
    },
    [USER_UPLOAD_AVATAR]({ getters }, avatar) {
        var FormData = require('form-data');
        var form = new FormData();
        form.append("avatar", avatar);
        let promise = axios.post(
            `/api/storage`,
            form,
            {
                headers: {
                    "Content": "multipart/form-data"
                }
            }
        );
        promise.then(({ data }) => {
            Profile.update({
                where: profile => profile.user_id == getters[AUTH_USER_ID],
                data
            })
        })
        return promise;
    },
    [USER_FOLLOW_TOGGLE]({ getters }, id) {
        let promise = axios.patch(`/api/users/${id}/toggle-follow`);
        promise.then(({ data }) => {
            User.update(data)
            Profile.update({
                where(profile) {
                    return profile.user_id == data.id;
                },
                data(profile) {
                    profile.followers_number += data.following ? +1 : -1;
                },
            });
            Profile.update({
                where(profile) {
                    return profile.user_id == getters[AUTH_USER_ID];
                },
                data(profile) {
                    profile.following_number += data.following ? +1 : -1;
                },
            });
        })
        return promise;
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
};
