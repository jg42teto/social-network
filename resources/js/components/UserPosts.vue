<template>
    <div>
        <b-nav class="mb-3" tabs fill>
            <b-nav-item
                v-for="tab in tabs"
                :key="tab.view"
                :active="activeTab(tab)"
                @click="clickTab(tab)"
            >
                {{ tab.label }}
            </b-nav-item>
        </b-nav>
        <post-page
            v-if="renderPostPage"
            :id="user.id"
            :action="action"
        ></post-page>
    </div>
</template>

<script>
import {
    POST_PAGE_ALL,
    POST_PAGE_WALL,
    POST_PAGE_LIKES,
} from "@/store/interface";
import PostPage from "./PostPage.vue";
import User from "@/models/User";

export default {
    components: { PostPage },
    data() {
        return {
            tabs: [
                {
                    view: "posts",
                    route: "user_posts",
                    label: "Posts",
                },
                {
                    view: "posts_and_replies",
                    route: "user_posts_and_replies",
                    label: "Posts & Replies",
                },
                {
                    view: "liked_posts",
                    route: "user_liked_posts",
                    label: "Likes",
                },
            ],
        };
    },
    props: {
        view: {
            default: "posts",
            validator(value) {
                return (
                    ["posts", "posts_and_replies", "liked_posts"].indexOf(
                        value
                    ) != -1
                );
            },
        },
    },
    computed: {
        action() {
            if (this.view == "posts_and_replies") {
                return POST_PAGE_ALL;
            }
            if (this.view == "liked_posts") {
                return POST_PAGE_LIKES;
            }
            return POST_PAGE_WALL;
        },
        username() {
            return this.$route.params.username;
        },
        user() {
            return User.query().where("username", this.username).first();
        },
        renderPostPage() {
            return !!this.user;
        },
    },
    methods: {
        activeTab(tab) {
            return tab.view == this.view;
        },
        clickTab(tab) {
            this.$router.push({
                name: tab.route,
                params: {
                    username: this.username,
                },
            });
        },
    },
};
</script>

<style>
</style>