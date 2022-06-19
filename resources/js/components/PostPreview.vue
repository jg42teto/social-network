<template>
    <b-card v-if="renderPost" no-body>
        <b-card-header>
            <div class="d-flex">
                <b-avatar
                    size="4rem"
                    :src="user.profile.avatar"
                    class="mt-2 mr-3 mb-1"
                    :to="{
                        name: 'user_posts',
                        params: {
                            username: this.user.username,
                        },
                    }"
                >
                </b-avatar>
                <div>
                    <b-card-title class="d-flex">
                        <router-link
                            :to="{
                                name: 'user_posts',
                                params: {
                                    username: this.user.username,
                                },
                            }"
                            >{{ this.user.profile.name }}</router-link
                        >
                    </b-card-title>
                    <b-card-sub-title class="mb-3">
                        <user-handle :user_id="user.id" />
                        <span v-if="hasSharedPost">quoted</span>
                    </b-card-sub-title>
                    <b-card-sub-title class="mb-2">
                        {{ this.postCreatedAt }}
                    </b-card-sub-title>
                </div>
            </div>
        </b-card-header>

        <div class="post-content-wrapper" @click="clickPostContentWrapper">
            <b-card-body>
                <b-card-text class="post-content-text">
                    <post-content :post_id="post_id" />
                </b-card-text>

                <div
                    v-if="hasSharedPost"
                    class="shared-post"
                    @click="clickSharedPost"
                >
                    <template v-if="display_shared_post">
                        <post-preview
                            v-if="renderSharedPost"
                            :post_id="post.shared_post_id"
                            :display_shared_post="false"
                            :display_controls="false"
                        />
                        <post-deleted v-else />
                    </template>
                    <router-link
                        v-else
                        :to="{
                            name: 'post',
                            params: {
                                id: post.shared_post_id,
                            },
                        }"
                        >See quoted post.</router-link
                    >
                </div>
            </b-card-body>
        </div>

        <b-card-footer v-if="display_controls">
            <post-controls
                :post_id="post_id"
                :redirect_when_delete="redirect_when_delete"
            />
        </b-card-footer>
    </b-card>
</template>

<script>
import date from "date-and-time";
import Post from "@/models/Post";
import User from "@/models/User";
import UserHandle from "./UserHandle.vue";
import PostDeleted from "./PostDeleted.vue";
import PostControls from "./PostControls.vue";
import PostContent from "./PostContent.vue";

export default {
    name: "post-preview",
    components: {
        UserHandle,
        PostDeleted,
        PostControls,
        PostContent,
    },
    props: {
        post_id: { required: true },
        redirect_when_delete: {},
        display_shared_post: { default: true },
        display_controls: { default: true },
    },
    computed: {
        post() {
            return Post.query().find(this.post_id);
        },
        renderPost() {
            return !!this.post;
        },
        hasSharedPost() {
            return !!this.post.shared_post_id;
        },
        renderSharedPost() {
            return Post.query().where("id", this.post.shared_post_id).exists();
        },
        user() {
            return User.query().with("profile").find(this.post.user_id);
        },
        postCreatedAt() {
            return date.format(
                new Date(Date.parse(this.post.created_at)),
                "DD/MM/YY HH:mm:ss"
            );
        },
    },
    methods: {
        clickPostContentWrapper() {
            this.$router.push({
                name: "post",
                params: {
                    username: this.user.username,
                    id: this.post_id,
                },
            });
        },
        clickSharedPost(event) {
            event.stopPropagation();
        },
    },
};
</script>

<style scoped>
.post-content-wrapper {
    cursor: pointer;
}
.post-content-text {
    font-size: 1.5rem;
}
.shared-post {
    cursor: auto;
}
</style>
