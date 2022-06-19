<template>
    <div>
        <post-form
            v-if="showForm"
            :parent-post-id="parentPostId"
            @successful-submit="submittedPostForm"
            class="my-3"
        />
        <post-wrapper
            v-for="id in page.items"
            :key="id"
            :id="id"
            :collapsed_at_first="isColapsedAtFirst(id)"
            class="my-3"
        />
        <div
            v-if="showPostLoadingSpinner"
            class="d-flex justify-content-center my-5"
        >
            <b-spinner
                variant="primary"
                type="grow"
                label="Spinning"
            ></b-spinner>
        </div>
    </div>
</template>

<script>
import PostForm from "./PostForm.vue";
import PostWrapper from "./PostWrapper.vue";
import {
    POST_PAGE_REPLIES,
    POST_PAGE_ALL,
    POST_PAGE_WALL,
    POST_INFO,
    AUTH_USER_ID,
    POST_PAGE_MENTION,
    USER_DATA_NOTIFICATIONS_CHECKED,
} from "@/store/interface";
import Paging from "@/models/Paging";

export default {
    components: { PostForm, PostWrapper },
    props: ["action", "id"],
    data() {
        return {
            loading_page: false,
            collapsed_at_first: null,
        };
    },
    created() {
        this.init_paging();
        window.addEventListener("scroll", this.handleScroll, { passive: true });
    },
    beforeDestroy() {
        window.removeEventListener("scroll", this.handleScroll);
    },
    computed: {
        page_id() {
            return this.action + this.id;
        },
        page() {
            return Paging.find(this.page_id);
        },
        parentPostId() {
            return this.action == POST_PAGE_REPLIES ? this.id : null;
        },
        showForm() {
            return (
                this.action == POST_PAGE_REPLIES ||
                ([POST_PAGE_ALL, POST_PAGE_WALL].indexOf(this.action) != -1 &&
                    this.id == this.$store.getters[AUTH_USER_ID])
            );
        },
        showPostLoadingSpinner() {
            return this.loading_page;
        },
    },
    watch: {
        page_id() {
            this.init_paging();
        },
    },
    methods: {
        handleScroll() {
            // enters <if block> if a next page isn't already loading and
            // if there is a next page link fetched by the previous call
            if (!this.loading_page && this.page.next_page !== null) {
                let mainColEl = document.getElementById("main-col");
                if (
                    window.scrollY + 3 * window.innerHeight >=
                    mainColEl.offsetHeight
                ) {
                    this.paging();
                }
            }
        },
        init_paging() {
            if (!this.page) {
                Paging.insert({
                    data: {
                        id: this.page_id,
                    },
                });
                this.$nextTick(() => {
                    this.paging().finally(() => {
                        this.$nextTick(() => {
                            if (
                                this.action === POST_PAGE_MENTION &&
                                this.page.items.length > 0
                            ) {
                                this.$store
                                    .dispatch(USER_DATA_NOTIFICATIONS_CHECKED, {
                                        last_checked_post_id:
                                            this.page.items[0],
                                    })
                                    .catch((err) => console.error(err));
                            }
                        });
                    });
                });
            }
        },
        paging() {
            this.loading_page = true;
            var ids;
            var next_page;
            return this.$store
                .dispatch(this.action, {
                    id: this.id,
                    page: this.page.next_page,
                })
                .then((res) => {
                    ids = res.data.data.map((d) => d.id);
                    next_page = res.data.next_page_url;
                    let post_ids = res.data.data.map((d) => d.post_id);
                    return this.$store.dispatch(POST_INFO, post_ids);
                })
                .then(() => {
                    Paging.update({
                        where: this.page.id,
                        data(page) {
                            page.next_page = next_page;
                            page.items.push(...ids);
                        },
                    });
                })
                .catch((err) => console.error(err))
                .finally(() => {
                    this.$nextTick(() => {
                        this.loading_page = false;
                    });
                });
        },
        submittedPostForm(id) {
            this.collapsed_at_first = id;
            this.page.items.unshift(id);
        },
        isColapsedAtFirst(id) {
            if (id == this.collapsed_at_first) {
                this.collapsed_at_first = null;
                return true;
            }
            return false;
        },
    },
};
</script>

<style>
</style>