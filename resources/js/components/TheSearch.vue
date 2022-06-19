<template>
    <autocomplete
        class="p-1"
        auto-select
        :search="search"
        :get-result-value="searchResultView"
        @submit="searchSubmit"
        :debounce-time="500"
    />
</template>

<script>
import { USER_SEARCH, HASHTAG_SEARCH } from "@/store/interface";
import Autocomplete from "@trevoreyre/autocomplete-vue";
import "@trevoreyre/autocomplete-vue/dist/style.css";

export default {
    components: {
        Autocomplete,
    },
    data() {
        return {
            searchTerm: "",
        };
    },
    methods: {
        search(input) {
            if (!input || input.length <= 0) return [];
            let action;
            if (input.charAt(0) == "#") {
                if (input.length == 1) return [];
                action = HASHTAG_SEARCH;
                input = input.substr(1);
            } else {
                action = USER_SEARCH;
            }
            return this.$store
                .dispatch(action, input)
                .then(({ data }) => data)
                .catch((err) => console.error(err));
        },
        searchResultView(result) {
            if (result.keyword) return "#" + result.keyword;
            return result.username + " (" + result.name + ")";
        },
        searchSubmit(result) {
            if (!result) return;
            if (result.username) {
                this.$router.push({
                    name: "user_posts",
                    params: {
                        username: result.username,
                    },
                });
            }
            if (result.keyword) {
                this.$router.push({
                    name: "hashtag_explorer",
                    params: {
                        hashtag: result.keyword,
                    },
                });
            }
        },
    },
};
</script>

<style>
.autocomplete-input,
.autocomplete-result {
    padding: 4px 4px 4px 40px;
    background-size: 16px;
}
.autocomplete-result-list {
    padding: 0 !important;
}
</style>