<template>
    <b-container fluid="xl" class="px-0">
        <b-row no-gutters>
            <b-col md="2">
                <the-sidebar class="sticky-top" />
            </b-col>
            <b-col id="main-col" md="8">
                <div id="content" class="px-3">
                    <router-view />
                </div>
            </b-col>
            <b-col md="2">
                <the-search class="sticky-top" />
            </b-col>
        </b-row>
    </b-container>
</template>

<script>
import {
    AUTH_LOGOUT,
    AUTH_AUTHENTICATED,
    USER_DATA_FETCH,
} from "@/store/interface";
import TheSidebar from "./TheSidebar";
import TheSearch from "./TheSearch";
import axios from "axios";

export default {
    components: {
        TheSidebar,
        TheSearch,
    },
    created() {
        axios.interceptors.response.use(undefined, (err) => {
            if (
                err.response.status === 401 &&
                this.$store.getters[AUTH_AUTHENTICATED]
            ) {
                this.$store
                    .dispatch(AUTH_LOGOUT)
                    .catch((err) => console.error(err));
            }
            return Promise.reject(err);
        });
        this.$store.dispatch(USER_DATA_FETCH);
    },
};
</script>
<style lang='scss'>
@import "#";
#content {
    position: relative;
}
#main-col {
    border: 0 1;
    border-style: solid;
    border-width: 0 1px;
    border-color: #ddd;
    min-height: 100vh;
}
</style>