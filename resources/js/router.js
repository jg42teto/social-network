import Vue from 'vue';
import VueRouter from 'vue-router';
import store from './store';
import Auth from './components/Auth';
import App from './components/app';
import Home from './components/Home';
import UserOverview from './components/UserOverview';
import UserPosts from './components/UserPosts';
import PostOverview from './components/PostOverview';
import AdminPanel from './components/AdminPanel';
import NotFound from './components/NotFound';
import HashtagExplorer from './components/HashtagExplorer'
import Notifications from './components/Notifications';
import {
    AUTH_AUTHENTICATED,
    AUTH_ADMIN,
} from '@/store/interface'

Vue.use(VueRouter);

const routes = [
    {
        path: '/login',
        name: 'login',
        component: Auth,
        props: {
            auth_type: 'login',
        },
    },
    {
        path: '/register',
        name: 'register',
        component: Auth,
        props: {
            auth_type: 'register',
        },
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: Auth,
        props: {
            auth_type: 'forgot-password',
        },
    },
    {
        path: '/reset-password/:token',
        name: 'reset-password',
        component: Auth,
        props: {
            auth_type: 'reset-password',
        },
    },
    {
        path: '/404',
        component: NotFound,
        name: 'not_found'
    },
    {
        path: '/', component: App, children: [
            {
                path: '',
                component: Home,
                name: 'home',
            },
            {
                path: 'hashtag/:hashtag',
                component: HashtagExplorer,
                name: 'hashtag_explorer',
            },
            {
                path: 'notifications',
                component: Notifications,
                name: 'notifications',
            },
            {
                path: 'admin',
                component: AdminPanel,
                name: 'admin_panel',
            },
            {
                path: ':username',
                component: UserOverview,
                children: [
                    {
                        path: '',
                        component: UserPosts,
                        props: {
                            view: 'posts',
                        },
                        name: 'user_posts'
                    },
                    {
                        path: 'with_replies',
                        component: UserPosts,
                        props: {
                            view: 'posts_and_replies',
                        },
                        name: 'user_posts_and_replies'
                    },
                    {
                        path: 'likes',
                        component: UserPosts,
                        props: {
                            view: 'liked_posts',
                        },
                        name: 'user_liked_posts'
                    },
                ]
            },
            {
                path: ':username/status/:id',
                component: PostOverview,
                name: 'post'
            },

        ]
    },
    { path: '*', redirect: '/404' },
];

const scrollBehavior = (to, from, savedPosition) => {
    if (
        to.params.username &&
        from.params.username &&
        to.params.username === from.params.username
    ) {
        return
    }

    if (to.name == "post") {
        return
    }

    if (savedPosition) {
        return savedPosition
    } else {
        return new Promise((resolve, reject) => {
            setTimeout(() => {
                resolve({
                    x: 0, y: 0,
                    behavior: 'smooth',
                })
            }, 250)
        })
    }
}

const router = new VueRouter({
    mode: 'history',
    routes,
    scrollBehavior,
});

router.beforeEach((to, from, next) => {
    if ([
        'login',
        'register',
        'forgot-password',
        'reset-password',
    ].indexOf(to.name) == -1 &&
        !store.getters[AUTH_AUTHENTICATED]) {
        next({ name: 'login' })
    }
    else if (to.name == 'admin_panel' && !store.getters[AUTH_ADMIN]) {
        next({ name: 'home' })
    }
    next()
})

export default router;

