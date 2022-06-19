import axios from "axios";
import {
    HASHTAG_SEARCH,
} from '@/store/interface'

const state = () => ({});

const getters = {};

const mutations = {};

const actions = {
    [HASHTAG_SEARCH](context, term) {
        return axios.get('/api/hashtag/search?term=' + term);
    }
};

export default {
    state,
    getters,
    actions,
    mutations,
};
