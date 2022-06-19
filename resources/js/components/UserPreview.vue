<template>
    <b-card v-if="renderUser" no-body>
        <b-card-header
            header-bg-variant="primary"
            header-text-variant="white"
            class="text-center"
        >
            <b-avatar
                size="6rem"
                button
                v-b-tooltip.hover.top="tooltipAvatar"
                @click="clickAvatar"
                :src="user.profile.avatar"
            >
            </b-avatar>
            <b-card-title> {{ user.profile.name }} </b-card-title>
            <b-card-sub-title sub-title-text-variant="white">
                {{ "@" + user.username }}
            </b-card-sub-title>
            <b-card-text>
                <icon-calendar />
                Joined
                {{ userJoined }}
                <br />
                <icon-mail />
                {{ user.email }}
            </b-card-text>
        </b-card-header>
        <b-card-body>
            <div>
                {{ user.profile.bio }}
            </div>
            <div class="mt-4">
                <span
                    ><strong>
                        {{ this.user.profile.followers_number }}
                    </strong>
                    Followers
                </span>
                <span class="ml-3">
                    <strong>
                        {{ this.user.profile.following_number }}
                    </strong>
                    Following
                </span>
            </div>
        </b-card-body>
        <b-card-body>
            <b-button
                v-if="renderEditButton"
                variant="primary"
                @click="clickEditButton"
                >Edit</b-button
            >
            <b-button
                v-if="renderFollowButton"
                variant="primary"
                @click="clickFollowButton"
            >
                {{ this.user.following ? "Unfollow" : "Follow" }}
            </b-button>
        </b-card-body>

        <b-modal
            id="upload-avatar-modal"
            title="Upload Your Avatar"
            @ok.prevent="clickOkUploadAvatarModal"
            @cancel="clickCancelUploadAvatarModal"
        >
            <b-form-group
                :state="!errors.avatar"
                :invalid-feedback="errors.avatar"
            >
                <b-form-file
                    v-model="avatar"
                    placeholder="Browse for avatar or drop it here..."
                    drop-placeholder="Drop file here..."
                ></b-form-file>
            </b-form-group>
        </b-modal>
        <b-modal
            id="edit-modal"
            title="Edit your profile"
            @ok.prevent="clickOkEditModal"
            @cancel="clickCancelEditModal"
        >
            <form ref="edit-form" @submit.prevent="submitEditForm">
                <b-form-group
                    label="Name"
                    :state="!errors.name"
                    :invalid-feedback="errors.name"
                >
                    <b-form-input
                        v-model="editForm.name"
                        placeholder="Your name (default name is username)..."
                    ></b-form-input>
                </b-form-group>
                <b-form-group
                    label="Bio"
                    :state="!errors.bio"
                    :invalid-feedback="errors.bio"
                >
                    <b-form-textarea
                        no-resize
                        rows="3"
                        v-model="editForm.bio"
                        placeholder="Write something about you..."
                    ></b-form-textarea>
                </b-form-group>
                <b-form-group
                    label="Email"
                    :state="!errors.email"
                    :invalid-feedback="errors.email"
                >
                    <b-form-input
                        type="email"
                        required
                        v-model="editForm.email"
                        placeholder="Your contact email..."
                    ></b-form-input>
                </b-form-group>
                <input type="submit" hidden />
            </form>
        </b-modal>
    </b-card>
</template>

<script>
import date from "date-and-time";
import { BIconCalendar2CheckFill, BIconEnvelopeFill } from "bootstrap-vue";
import {
    USER_EDIT,
    USER_UPLOAD_AVATAR,
    USER_FOLLOW_TOGGLE,
    AUTH_USER_ID,
} from "@/store/interface";
import User from "@/models/User";
import Vue from "vue";

export default {
    components: {
        "icon-calendar": BIconCalendar2CheckFill,
        "icon-mail": BIconEnvelopeFill,
    },
    props: {
        username: { require: true },
    },
    data() {
        return {
            avatar: null,
            editForm: {
                name: "",
                bio: "",
                email: "",
            },
            errors: {
                name: "",
                bio: "",
                email: "",
                avatar: "",
            },
        };
    },
    computed: {
        user() {
            return User.query()
                .with("profile")
                .where("username", this.username)
                .first();
        },
        userJoined() {
            return date.format(
                new Date(Date.parse(this.user.created_at)),
                "MMMM YYYY"
            );
        },
        isLoggedUser() {
            return this.user.id == this.$store.getters[AUTH_USER_ID];
        },
        tooltipAvatar() {
            return this.isLoggedUser ? "Edit Avatar" : null;
        },
        renderUser() {
            return !!this.user;
        },
        renderEditButton() {
            return this.isLoggedUser;
        },
        renderFollowButton() {
            return !this.isLoggedUser;
        },
    },
    methods: {
        clickAvatar() {
            if (!this.isLoggedUser) return;
            this.$bvModal.show("upload-avatar-modal");
        },
        clickOkUploadAvatarModal() {
            this.$store
                .dispatch(USER_UPLOAD_AVATAR, this.avatar)
                .then(() => this.$bvModal.hide("upload-avatar-modal"))
                .catch((err) => {
                    let errors = err?.response?.data?.errors;
                    if (errors) {
                        Vue.set(
                            this.errors,
                            "avatar",
                            errors.avatar ? errors.avatar[0] : ""
                        );
                    }
                    console.error(err);
                });
        },
        clickCancelUploadAvatarModal() {
            this.$data.errors = this.$options.data().errors;
        },
        clickOkEditModal() {
            this.$refs["edit-form"].requestSubmit();
        },
        clickCancelEditModal() {
            this.$data.errors = this.$options.data().errors;
        },
        submitEditForm() {
            let data = {};
            if (this.editForm.name != this.user.profile.name)
                data.name = this.editForm.name;
            if (this.editForm.bio != this.user.profile.bio)
                data.bio = this.editForm.bio;
            if (this.editForm.email != this.user.email)
                data.email = this.editForm.email;
            this.$store
                .dispatch(USER_EDIT, data)
                .then(() =>
                    this.$nextTick(() => {
                        this.$bvModal.hide("edit-modal");
                    })
                )
                .catch((err) => {
                    let errors = err?.response?.data?.errors;
                    if (errors) {
                        Vue.set(
                            this.errors,
                            "email",
                            errors.email ? errors.email[0] : ""
                        );
                        Vue.set(
                            this.errors,
                            "bio",
                            errors.bio ? errors.bio[0] : ""
                        );
                        Vue.set(
                            this.errors,
                            "name",
                            errors.name ? errors.name[0] : ""
                        );
                    }
                    console.error(err);
                });
        },
        clickFollowButton() {
            this.$store.dispatch(USER_FOLLOW_TOGGLE, this.user.id);
        },
        clickEditButton() {
            this.editForm.name = this.user.profile.name;
            this.editForm.bio = this.user.profile.bio;
            this.editForm.email = this.user.email;
            this.$bvModal.show("edit-modal");
        },
    },
};
</script>

<style>
</style>