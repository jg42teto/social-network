<template>
    <div class="pt-3">
        <template v-for="id in parents_ids">
            <post-wrapper :key="id" :id="id" :show_deleted="true" />
            <div><div class="pb-3 ml-3 border-left border-mute"></div></div>
        </template>
        <post-preview
            ref="post"
            :post_id="post_id"
            class="mb-3"
            :redirect_when_delete="true"
        />
        <post-page :action="action" :id="post_id" />
    </div>
</template>

<script>
import PostWrapper from "./PostWrapper.vue";
import PostPage from "./PostPage.vue";
import PostPreview from "./PostPreview.vue";
import {
    POST_PAGE_REPLIES,
    POST_REPLIED_TO,
    POST_INFO,
} from "@/store/interface";
import Post from "@/models/Post";

export default {
    components: {
        PostWrapper,
        PostPage,
        PostPreview,
    },
    data() {
        return {
            parents_ids: [],
            scroll: {
                y: 0,
                timerId: 0,
                interval: 500,
            },
        };
    },
    created() {
        this.fetchParents();
    },
    mounted() {
        window.addEventListener("resize", this.resizeCallback);

        // tracking vertical scroll
        this.scroll.timerId = setInterval(
            () => (this.scroll.y = window.scrollY),
            this.scroll.interval
        );
    },
    destroyed() {
        window.removeEventListener("resize", this.resizeCallback);
        clearInterval(this.scroll.timerId);
    },
    computed: {
        action() {
            return POST_PAGE_REPLIES;
        },
        meta_post_id() {
            return this.meta_post.id;
        },
        post_id() {
            return this.$route.params.id;
        },
    },
    watch: {
        parents_ids() {
            this.$nextTick(() => {
                this.addSpace();
                window.scrollTo({
                    top: this.$refs.post.$el.offsetTop,
                });
            });
        },
        post_id() {
            this.fetchParents();
        },
    },
    methods: {
        fetchParents() {
            var parents_ids;
            this.$store
                .dispatch(POST_REPLIED_TO, this.post_id)
                .then(({ data }) => {
                    let post = Post.query().with("user").find(this.post_id);
                    if (!post) {
                        this.$router.replace({ name: "not_found" });
                        throw new Error(
                            `Post with id ${this.post_id} not found!`
                        );
                    }

                    if (this.$route.params.username !== post.user.username) {
                        this.$router.replace({
                            name: "post",
                            params: {
                                username: post.user.username,
                                id: this.post_id,
                            },
                        });
                    }
                    parents_ids = data.map((p) => p?.id);
                    if (parents_ids.length == 0) {
                        this.$router.replace({ name: "not_found" });
                    }
                    parents_ids.shift();
                    parents_ids.reverse();
                    let post_ids = data.map((p) => p?.post_id);
                    return this.$store.dispatch(POST_INFO, post_ids);
                })
                .then(() => {
                    this.parents_ids = parents_ids;
                })
                .catch((err) => {
                    console.error(err);
                });
        },
        addSpace() {
            let mainColEl = document.getElementById("main-col");
            mainColEl.style.minHeight =
                this.$refs.post.$el.offsetTop + window.innerHeight + "px";
        },
        resizeCallback() {
            this.addSpace();
            window.scrollTo({
                top: this.scroll.y + 1,
            });
        },
    },
};
</script>

<style>
</style>