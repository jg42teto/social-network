<template>
    <div>
        <div class="pb-2 mt-4 mb-2 border-bottom">
            <h2>Explore <hashtag-handle :hashtag="hashtag" /></h2>
        </div>
        <post-page :action="action" :id="hashtag" />
    </div>
</template>

<script>
import PostPage from "./PostPage";
import HashtagHandle from "./HashtagHandle";
import { POST_PAGE_HASHTAG } from "@/store/interface";
export default {
    components: {
        PostPage,
        HashtagHandle,
    },
    computed: {
        action() {
            return POST_PAGE_HASHTAG;
        },
        hashtag() {
            return this.$route.params.hashtag;
        },
    },
};
</script>

<style>
</style>