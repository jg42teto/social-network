<template>
    <span
        ><router-link
            :to="{
                name: 'hashtag_explorer',
                params: { hashtag },
            }"
            >#{{ hashtag }}</router-link
        ></span
    >
</template>

<script>
import User from "@/models/User";
export default {
    props: {
        hashtag: { required: true },
    },
};
</script>

<style>
</style>