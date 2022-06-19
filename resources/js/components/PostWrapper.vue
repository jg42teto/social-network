<template>
    <b-collapse v-if="renderWrapper" v-model="expandedWrapper">
        <repost-header v-if="renderRepostHeader" :user_id="metaPost.user_id" />
        <post-preview
            :post_id="metaPost.post_id"
            :redirect_when_delete="redirect_when_delete"
        />
    </b-collapse>
    <post-deleted v-else-if="show_deleted" />
</template>

<script>
import PostDeleted from "./PostDeleted.vue";
import PostPreview from "./PostPreview.vue";
import RepostHeader from "./RepostHeader.vue";
import MetaPost from "@/models/MetaPost";

export default {
    components: {
        RepostHeader,
        PostPreview,
        PostDeleted,
    },
    data() {
        return {
            expandedWrapper: !this.collapsed_at_first,
        };
    },
    props: {
        id: { requred: true },
        collapsed_at_first: { type: Boolean },
        show_deleted: { type: Boolean, default: false },
        redirect_when_delete: {},
    },
    computed: {
        metaPost() {
            return MetaPost.query().find(this.id);
        },
        renderWrapper() {
            return !!this.metaPost;
        },
        renderRepostHeader() {
            return this.metaPost.repost;
        },
    },
    mounted() {
        this.expandedWrapper = true;
    },
    watch: {
        metaPost() {
            this.expandedWrapper = !!this.metaPost;
        },
    },
};
</script>

<style>
.collapse-leave-active {
    transition: collapse-in 1s;
}

@keyframes collapse-in {
    0% {
        transform: translateY(0);
    }
    100% {
        transform: translateY(100px);
    }
}
</style>