import axios from "axios";
import {
    AUTH_USER_ID,
    POST_ADD,
    POST_FETCH,
    POST_PAGE_ALL,
    POST_PAGE_WALL,
    POST_PAGE_REPLIES,
    POST_PAGE_LIKES,
    POST_PAGE_HOME,
    POST_PAGE_HASHTAG,
    POST_REPLIED_TO,
    POST_LIKE_TOGGLE,
    POST_REPOST_TOGGLE,
    POST_DELETE,
    POST_INFO,
    POST_PAGE_MENTION,
} from '@/store/interface';
import Post from '@/models/Post';
import MetaPost from '@/models/MetaPost';

const state = () => ({});

const getters = {};

const mutations = {};

const actions = {
    [POST_ADD](context, data) {
        var shared_post_id = data.shared_post_id;
        let promise = axios.post('/api/posts', data);
        promise.then(({ data }) => {
            if (shared_post_id) {
                Post.update({
                    where: shared_post_id,
                    data(post) {
                        post.shares_number += 1;
                    }
                })
            }
            MetaPost.insertOrUpdate({ data });
        });
        return promise;
    },
    [POST_FETCH](context, { page, path }) {
        let promise;
        if (page) {
            promise = axios.get(page);
        } else {
            promise = axios.get(path);
        }
        promise.then((res) => {
            MetaPost.insertOrUpdate({ data: res.data.data });
        });
        return promise;
    },
    [POST_PAGE_ALL]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/users/${id}/posts/all` });
    },
    [POST_PAGE_WALL]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/users/${id}/posts/wall` });
    },
    [POST_PAGE_LIKES]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/users/${id}/posts/liked` });
    },
    [POST_PAGE_REPLIES]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/posts/${id}/replies` });
    },
    [POST_PAGE_HOME]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: '/api/posts' });
    },
    [POST_PAGE_HASHTAG]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/hashtag/${id}/posts` });
    },
    [POST_PAGE_MENTION]({ dispatch }, { page, id }) {
        return dispatch(POST_FETCH, { page, path: `/api/posts/mentions` });
    },
    [POST_REPLIED_TO](context, id) {
        let promise = axios.get(`/api/posts/${id}/ancestry `);
        promise.then(({ data }) => {
            MetaPost.insertOrUpdate({ data });
        });
        return promise;
    },
    [POST_LIKE_TOGGLE](context, id) {
        let promise = axios.patch(`/api/posts/${id}/toggle-like`);
        promise.then(({ data }) => {
            Post.update({
                where: data.id,
                data(post) {
                    post.liked = data.liked;
                    post.likes_number += data.liked ? 1 : -1;
                }
            });
        });
        return promise;
    },
    [POST_REPOST_TOGGLE]({ getters }, id) {
        let promise = axios.patch(`/api/posts/${id}/toggle-repost`);
        promise.then(({ data }) => {
            Post.update({
                where: data.id,
                data(post) {
                    post.shared = data.shared;
                    post.shares_number += data.shared ? 1 : -1;
                }
            });
            if (!data.shared) {
                MetaPost.delete((meta) => {
                    return meta.user_id == getters[AUTH_USER_ID] &&
                        meta.post_id == id &&
                        meta.repost == true
                })
            }
        });
        return promise;
    },
    [POST_DELETE]({ commit }, id) {
        let promise = axios.delete(`/api/posts/${id}`);
        promise.then(() => {
            MetaPost.delete((meta) => {
                return meta.post_id == id;
            });
            let post = Post.find(id);
            if (post.shared_post_id) {
                Post.update({
                    where: post.shared_post_id,
                    data(shared_post) {
                        shared_post.shares_number -= 1;
                    }
                })
            }
            Post.delete(id);
        });
        return promise;
    },
    [POST_INFO](context, ids) {
        let promise = axios.get('/api/posts/info?ids=' + ids.join(','));
        promise.then(({ data }) => {
            Post.update(data);
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
