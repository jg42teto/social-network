<template>
    <div class="d-flex align-items-center justify-content-around">
        <div class="d-flex align-items-center">
            <span class="circle">
                <icon-comment @click="clickIconComment" />
            </span>
            <span class="ml-2">
                {{ post.comments_number }}
            </span>
        </div>

        <div class="d-flex align-items-center">
            <span class="circle" :id="shareControlId" tabindex="-1">
                <icon-shared variant="info" v-if="post.shared" />
                <icon-share v-else />
            </span>
            <span class="ml-2">
                {{ post.shares_number }}
            </span>
            <b-popover
                :target="shareControlId"
                placement="left"
                triggers="focus"
                custom-class="p-0 popover-share"
            >
                <b-list-group>
                    <b-list-group-item button @click="clickRepost"
                        >{{
                            post.shared ? "Undo" : ""
                        }}
                        Repost</b-list-group-item
                    >
                    <b-list-group-item button @click="clickQuote"
                        >Quote</b-list-group-item
                    >
                </b-list-group>
            </b-popover>
            <b-modal
                ref="post-quote-modal"
                title="Quote post"
                centered
                ok-only
                ok-title="Close"
            >
                <post-form
                    :sharedPostId="post_id"
                    @successful-submit="submittedQuotePostForm"
                />
            </b-modal>
        </div>

        <div class="d-flex align-items-center">
            <span class="circle" @click="clickLike">
                <icon-liked variant="danger" v-if="post.liked" />
                <icon-like v-else />
            </span>
            <span class="ml-2">
                {{ post.likes_number }}
            </span>
        </div>

        <div v-if="renderIconDelete">
            <span class="circle" @click="clickIconDelete">
                <icon-delete variant="danger" />
            </span>
            <b-modal
                ref="post-delete-modal"
                title="Deleting post"
                centered
                ok-title="Delete"
                ok-variant="danger"
                @ok="clickOkPostDeleteModel"
            >
                Are you sure you want to delete post?
                {{
                    redirect_when_delete
                        ? "You'll be redirected to the home page."
                        : ""
                }}
            </b-modal>
        </div>
    </div>
</template>

<script>
import {
    BIconChat,
    BIconHeart,
    BIconHeartFill,
    BIconShare,
    BIconShareFill,
    BIconTrash,
} from "bootstrap-vue";
import {
    AUTH_USER_ID,
    AUTH_ADMIN,
    AUTH_SUPERADMIN,
    POST_LIKE_TOGGLE,
    POST_REPOST_TOGGLE,
    POST_DELETE,
} from "@/store/interface";
import PostForm from "./PostForm.vue";
import Post from "@/models/Post";
import User from "@/models/User";

export default {
    props: {
        post_id: { required: true },
        redirect_when_delete: {},
    },
    components: {
        "icon-delete": BIconTrash,
        "icon-comment": BIconChat,
        "icon-like": BIconHeart,
        "icon-liked": BIconHeartFill,
        "icon-share": BIconShare,
        "icon-shared": BIconShareFill,
        PostForm,
    },
    computed: {
        post() {
            return Post.query().find(this.post_id);
        },
        user() {
            return User.query().with("profile").find(this.post.user_id);
        },
        renderIconDelete() {
            return (
                this.user.id == this.$store.getters[AUTH_USER_ID] ||
                (this.$store.getters[AUTH_ADMIN] && this.user.admin < 1) ||
                (this.$store.getters[AUTH_SUPERADMIN] && this.user.admin < 2)
            );
        },
        shareControlId() {
            return "share-control-" + this._uid;
        },
    },
    methods: {
        clickRepost() {
            this.$store.dispatch(POST_REPOST_TOGGLE, this.post_id);
        },
        clickQuote() {
            this.$refs["post-quote-modal"].show();
        },
        clickLike() {
            this.$store.dispatch(POST_LIKE_TOGGLE, this.post_id);
        },
        clickIconComment() {
            this.$router.push({
                name: "post",
                params: {
                    username: this.user.username,
                    id: this.post_id,
                },
            });
        },
        clickIconDelete() {
            this.$refs["post-delete-modal"].show();
        },
        clickOkPostDeleteModel() {
            this.$store
                .dispatch(POST_DELETE, this.post_id)
                .then(() => {
                    if (this.redirect_when_delete) {
                        this.$router.push({ name: "home" });
                    }
                })
                .catch((err) => console.error(err));
        },
        submittedQuotePostForm(id) {
            this.$refs["post-quote-modal"].hide();
        },
    },
};
</script>

<style scoped>
.circle {
    border-radius: 50px;
    height: 1.75rem;
    width: 1.75rem;
    display: inline-flex;
    justify-content: center;
    align-items: center;
}

.circle:hover {
    background-color: #bbbb;
    cursor: pointer;
}

.bi {
    font-size: 1.25rem;
}

.popover-share {
    z-index: auto;
}
</style>


<style>
.popover-share > .popover-body {
    padding: 0 !important;
}
</style>