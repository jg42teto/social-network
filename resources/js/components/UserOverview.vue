<template>
    <div>
        <b-row class="colored">
            <b-col> </b-col>
            <b-col md="8">
                <user-preview :username="username" class="my-4" />
            </b-col>
            <b-col> </b-col>
        </b-row>
        <router-view />
    </div>
</template>

<script>
import { USER_FETCH } from "@/store/interface";
import UserPreview from "./UserPreview";

export default {
    components: {
        UserPreview,
    },
    computed: {
        username() {
            return this.$route.params.username;
        },
    },
    created() {
        this.fetchUser();
    },
    methods: {
        fetchUser() {
            this.$store
                .dispatch(USER_FETCH, this.username)
                .then(({ data }) => {
                    if (data.id === undefined) {
                        this.$router.replace({ name: "not_found" });
                    }
                })
                .catch((err) => console.error(err));
        },
    },
    watch: {
        username() {
            this.fetchUser();
        },
    },
};
</script>

<style>
</style>