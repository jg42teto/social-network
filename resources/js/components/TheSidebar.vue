<template>
    <b-list-group flush>
        <b-list-group-item @click="clickLogo" button class="font-weight-bold">
            SocialNetwork
        </b-list-group-item>
        <b-list-group-item
            v-if="renderAdminPanelLink"
            @click="clickAdminPanel"
            button
        >
            Admin Panel
        </b-list-group-item>
        <b-list-group-item @click="clickHome" button>
            <!--  -->
            Home
        </b-list-group-item>
        <b-list-group-item @click="clickProfile" button>
            Profile
        </b-list-group-item>
        <b-list-group-item
            @click="clickNotifications"
            class="d-flex justify-content-between align-items-center"
            button
        >
            Notifications
            <b-badge v-if="notificationsCount" pill variant="danger">{{
                notificationsCount
            }}</b-badge>
        </b-list-group-item>
        <b-list-group-item @click="clickChangePassword" button>
            Change Password
            <password-change-dialog ref="passwordChangeDialog" />
        </b-list-group-item>
        <b-list-group-item @click="clickLogout" button>
            Logout
        </b-list-group-item>
    </b-list-group>
</template>

<script>
import {
    AUTH_LOGOUT,
    AUTH_USER_USERNAME,
    AUTH_ADMIN,
    USER_DATA_NOTIFICATIONS_NUMBER,
} from "@/store/interface";
import PasswordChangeDialog from "./PasswordChangeDialog.vue";

export default {
    components: { PasswordChangeDialog },
    computed: {
        notificationsCount() {
            return this.$store.getters[USER_DATA_NOTIFICATIONS_NUMBER];
        },
        renderAdminPanelLink() {
            return this.$store.getters[AUTH_ADMIN];
        },
    },
    methods: {
        clickAdminPanel() {
            this.$router.push({
                name: "admin_panel",
            });
        },
        clickLogo() {
            this.$router.push({
                name: "home",
            });
        },
        clickLogout() {
            this.$store
                .dispatch(AUTH_LOGOUT)
                .catch((err) => console.error(err));
        },
        clickChangePassword() {
            this.$refs.passwordChangeDialog.show();
        },
        clickProfile() {
            this.$router.push({
                name: "user_posts",
                params: {
                    username: this.$store.getters[AUTH_USER_USERNAME],
                },
            });
        },
        clickNotifications() {
            this.$router.push({
                name: "notifications",
            });
        },
        clickHome() {
            this.$router.push({
                name: "home",
            });
        },
    },
};
</script>

<style>
</style>