<template>
    <b-modal
        id="change-password-modal"
        title="Change your password"
        @ok="clickOkChangePasswordModal"
    >
        <form ref="change-password-form" @submit="submitChangePasswordForm">
            <b-form-group label="New Password">
                <b-form-input
                    type="password"
                    v-model="changePasswordForm.password"
                    required
                ></b-form-input>
            </b-form-group>
            <b-form-group
                label="Confirm Password"
                :state="
                    changePasswordForm.password ==
                    changePasswordForm.password_confirmation
                "
                invalid-feedback="Password doesn't match."
            >
                <b-form-input
                    type="password"
                    v-model="changePasswordForm.password_confirmation"
                    required
                ></b-form-input>
            </b-form-group>
            <input type="submit" hidden />
        </form>
    </b-modal>
</template>

<script>
import { USER_EDIT } from "@/store/interface";
export default {
    data() {
        return {
            changePasswordForm: {
                password: "",
                password_confirmation: "",
            },
        };
    },
    methods: {
        show() {
            this.$bvModal.show("change-password-modal");
        },
        clickOkChangePasswordModal(event) {
            if (event) event.preventDefault();
            this.$refs["change-password-form"].requestSubmit();
        },
        submitChangePasswordForm(event) {
            if (event) event.preventDefault();
            this.$store
                .dispatch(USER_EDIT, this.changePasswordForm)
                .then(() =>
                    this.$nextTick(() => {
                        this.$bvModal.hide("change-password-modal");
                    })
                )
                .catch((err) => console.error(err));
        },
        clickOkChangePasswordModal(event) {
            if (event) event.preventDefault();
            this.$refs["change-password-form"].requestSubmit();
        },
        submitChangePasswordForm(event) {
            if (event) event.preventDefault();
            this.$store
                .dispatch(USER_EDIT, this.changePasswordForm)
                .then(() =>
                    this.$nextTick(() => {
                        this.$bvModal.hide("change-password-modal");
                    })
                )
                .catch((err) => console.error(err));
        },
    },
};
</script>

<style>
</style>