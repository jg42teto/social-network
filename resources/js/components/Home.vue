<template>
    <div>
        <b-jumbotron
            class="my-3 text-center"
            header="Social Network"
            lead="Explore the world of our social media app and share some thoughts for world to see."
        >
        </b-jumbotron>
        <post-page :action="action" :id="user_id" />
    </div>
</template>

<script>
import PostPage from "./PostPage";
import { POST_PAGE_HOME, AUTH_USER_ID } from "@/store/interface";
export default {
    components: {
        PostPage,
    },
    computed: {
        action() {
            return POST_PAGE_HOME;
        },
        user_id() {
            return this.$store.getters[AUTH_USER_ID];
        },
    },
};
</script>

<style>
</style>