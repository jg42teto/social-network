<template>
    <div>
        <b-container>
            <b-row>
                <b-col offset="3" cols="6">
                    <b-card class="my-5" no-body>
                        <b-card-header>
                            <b-nav card-header tabs>
                                <b-nav-item
                                    @click="clickTab('login')"
                                    :active="auth_type == 'login'"
                                    >Login</b-nav-item
                                >
                                <b-nav-item
                                    @click="clickTab('register')"
                                    :active="auth_type == 'register'"
                                    >Register</b-nav-item
                                >
                                <b-nav-item
                                    @click="clickTab('forgot-password')"
                                    :active="auth_type == 'forgot-password'"
                                    >Forgot Password</b-nav-item
                                >
                            </b-nav>
                        </b-card-header>

                        <b-card-body class="text-center">
                            <b-form @submit="submit" @reset="reset">
                                <b-form-group
                                    v-if="renderUsernameInput"
                                    label="Username:"
                                    label-for="input-username"
                                >
                                    <b-form-input
                                        id="input-username"
                                        v-model="form.username"
                                        type="text"
                                        placeholder="Enter username"
                                        required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group
                                    v-if="renderEmailInput"
                                    label="Email address:"
                                    label-for="input-email"
                                >
                                    <b-form-input
                                        v-model="form.email"
                                        type="email"
                                        placeholder="Enter email"
                                        required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group
                                    v-if="renderPasswordInput"
                                    label="Pasword:"
                                    label-for="input-password"
                                >
                                    <b-form-input
                                        id="input-password"
                                        v-model="form.password"
                                        type="password"
                                        placeholder="Password"
                                        required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group
                                    v-if="renderPasswordConfiramtionInput"
                                    label="Retype Password:"
                                    label-for="input-retype-password"
                                >
                                    <b-form-input
                                        id="input-retype-password"
                                        v-model="form.password_confirmation"
                                        type="password"
                                        placeholder="Password"
                                        required
                                    ></b-form-input>
                                </b-form-group>
                                <b-form-group v-if="renderKeepLoggedIn">
                                    <b-form-checkbox v-model="form.remember"
                                        >Keep logged in?</b-form-checkbox
                                    >
                                </b-form-group>
                                <b-button type="submit" variant="primary"
                                    >Submit</b-button
                                >
                                <b-button type="reset" variant="danger"
                                    >Reset</b-button
                                >
                            </b-form>
                            <b-alert
                                :show="!!error"
                                variant="danger"
                                class="my-3"
                            >
                                {{ this.error }}
                            </b-alert>
                            <b-alert
                                :show="!!info"
                                variant="primary"
                                class="my-3"
                            >
                                {{ this.info }}
                            </b-alert>
                        </b-card-body>
                    </b-card>
                </b-col>
            </b-row>
        </b-container>
    </div>
</template>

<script>
import { AUTH_REQUEST } from "@/store/interface";

export default {
    data() {
        return {
            error: "",
            form: {
                remember: true,
                username: "",
                email: "",
                password: "",
                password_confirmation: "",
            },
            info: "",
        };
    },
    computed: {
        renderUsernameInput() {
            return true;
        },
        renderEmailInput() {
            return this.auth_type == "register";
        },
        renderPasswordInput() {
            return this.auth_type != "forgot-password";
        },
        renderPasswordConfiramtionInput() {
            return (
                this.auth_type == "register" ||
                this.auth_type == "reset-password"
            );
        },
        renderTokenInput() {
            return this.auth_type == "reset-password";
        },
        renderKeepLoggedIn() {
            return this.auth_type == "login" || this.auth_type == "register";
        },
        resetPasswordToken() {
            return this.$route.params.token;
        },
    },
    props: {
        auth_type: {},
    },
    methods: {
        submit(event) {
            if (event) event.preventDefault();
            if (this.auth_type == "reset-password") {
                this.form.token = this.resetPasswordToken;
            }
            this.$store
                .dispatch(AUTH_REQUEST, {
                    data: this.form,
                    auth_type: this.auth_type,
                })
                .then(({ data }) => {
                    this.reset();
                    if (this.auth_type == "forgot-password") {
                        this.info = data.message;
                    } else if (this.auth_type == "reset-password") {
                        this.info = data.message;
                        this.$router.push({ name: "login" });
                    } else {
                        this.$router.push({ name: "home" });
                    }
                })
                .catch((err) => {
                    this.reset(null, false);
                    let message = err.response.data.message;
                    Object.values(err.response.data.errors).forEach(
                        (e) => (message += " " + e)
                    );
                    this.error = message;
                });
        },
        reset(event, form = true, alerts = true) {
            if (event) event.preventDefault();
            if (alerts) {
                this.$data.error = this.$options.data().error;
                this.$data.info = this.$options.data().info;
            }
            if (form) {
                this.$data.form = this.$options.data().form;
            }
        },
        clickTab(tab) {
            if (tab != this.auth_type) {
                this.reset();
                this.$router.push({ name: tab });
            }
        },
    },
};
</script>
