<template>
    <span
        ><router-link
            :to="{
                name: 'user_posts',
                params: {
                    username: user.username,
                },
            }"
            >{{ "@" + user.username }}</router-link
        ></span
    >
</template>

<script>
import User from "@/models/User";
export default {
    props: {
        user_id: { required: true },
    },
    computed: {
        user() {
            return User.query().find(this.user_id);
        },
    },
};
</script>

<style>
</style>