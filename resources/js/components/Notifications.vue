<template>
    <div>
        <div class="pb-2 mt-4 mb-2 border-bottom">
            <h2>Notifications</h2>
        </div>
        <post-page :action="action" />
    </div>
</template>

<script>
import PostPage from "./PostPage";
import { POST_PAGE_MENTION } from "@/store/interface";
export default {
    components: {
        PostPage,
    },
    computed: {
        action() {
            return POST_PAGE_MENTION;
        },
    },
};
</script>

<style>
</style>