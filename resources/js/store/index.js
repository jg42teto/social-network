import Vue from 'vue'
import Vuex from 'vuex'
import VuexORM from '@vuex-orm/core'
import { createStore } from 'vuex-extensions'
import auth from './modules/auth'
import posts from './modules/posts'
import users from './modules/users'
import user_data from './modules/user_data'
import admins from './modules/admins'
import hashtag from './modules/hashtag'
import Post from '@/models/Post'
import User from '@/models/User'
import Profile from '@/models/Profile'
import MetaPost from '@/models/MetaPost'
import Paging from '@/models/Paging'

Vue.use(Vuex);

const modules = { auth, posts, users, user_data, admins, hashtag };

const database = new VuexORM.Database();
database.register(Post);
database.register(User);
database.register(Profile)
database.register(MetaPost)
database.register(Paging)

export default createStore(Vuex.Store, {
    modules,
    plugins: [VuexORM.install(database)]
});