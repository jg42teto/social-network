<template>
    <b-card no-body class="my-3">
        <b-card-header
            header="Add new post"
            header-bg-variant="primary"
            header-text-variant="light"
        ></b-card-header>
        <b-card-body>
            <b-form @submit="submit">
                <b-form-group
                    :state="!errors.content"
                    :invalid-feedback="errors.content"
                >
                    <b-input-group>
                        <b-form-textarea
                            no-resize
                            rows="3"
                            id="input-content"
                            v-model="form.content"
                            placeholder="Write down your thougths..."
                            autocomplete="off"
                        >
                        </b-form-textarea>
                        <b-input-group-append>
                            <b-button type="submit" variant="primary"
                                >Add</b-button
                            >
                        </b-input-group-append>
                    </b-input-group>
                </b-form-group>
            </b-form>
        </b-card-body>
    </b-card>
</template>

<script>
import { POST_ADD } from "@/store/interface";
import Vue from "vue";

export default {
    data() {
        return {
            form: {
                content: "",
            },
            errors: {
                content: "",
            },
        };
    },
    props: {
        parentPostId: { default: null },
        sharedPostId: { default: null },
    },
    methods: {
        submit(event) {
            event.preventDefault();
            this.$store
                .dispatch(POST_ADD, {
                    ...this.form,
                    parent_post_id: this.parentPostId,
                    shared_post_id: this.sharedPostId,
                })
                .then(({ data }) => {
                    this.errors.content = "";
                    this.form.content = "";
                    this.$emit("successful-submit", data.id);
                })
                .catch((err) => {
                    let errors = err?.response?.data?.errors;
                    if (errors) {
                        Vue.set(
                            this.errors,
                            "content",
                            errors.content ? errors.content[0] : ""
                        );
                    }
                    console.error(err);
                });
        },
    },
};
</script>

<style>
</style>