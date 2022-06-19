<template>
    <div>
        <h2 class="my-3 py-2 border-bottom">Administrators</h2>
        <b-table-simple outlined hover small caption-top class="text-center">
            <b-thead head-variant="light">
                <b-tr>
                    <b-th>ID</b-th>
                    <b-th>Username</b-th>
                    <b-th>Email</b-th>
                    <b-th :colspan="adminsTableActionsColumnSpan">Actions</b-th>
                </b-tr>
            </b-thead>
            <b-tbody>
                <b-tr v-for="admin in admins" :key="admin.id">
                    <b-td>{{ admin.id }}</b-td>
                    <b-td>{{ admin.username }}</b-td>
                    <b-td>{{ admin.email }}</b-td>
                    <b-td>
                        <b-button
                            size="sm"
                            @click="clickProfileAction(admin)"
                            variant="primary"
                            >Profile</b-button
                        >
                    </b-td>
                    <b-td v-if="renderToggleAdminAction">
                        <b-button
                            size="sm"
                            v-if="admin.admin < 2"
                            @click="clickToggleAdmin(admin)"
                            :variant="admin.admin == 1 ? 'danger' : 'success'"
                            >{{ admin.admin == 1 ? "Remove" : "Add" }}</b-button
                        >
                    </b-td>
                </b-tr>
                <b-tr v-if="renderAddAdminRow">
                    <b-td></b-td>
                    <b-td>
                        <b-form-input
                            v-b-tooltip.hover.left
                            title="Type username"
                            v-model="new_admin_username"
                            size="sm"
                            class="text-center"
                        ></b-form-input>
                    </b-td>
                    <b-td></b-td>
                    <b-td :colspan="adminsTableActionsColumnSpan">
                        <b-button
                            v-b-tooltip.hover.right
                            title="Add new admin"
                            variant="white"
                            size="sm"
                            @click="clickAddNewAdmin"
                            :disabled="!new_admin_username"
                            ><icon-add scale="2" />
                        </b-button>
                    </b-td>
                </b-tr>
            </b-tbody>
        </b-table-simple>

        <h2 class="mb-3 mt-4 py-2 border-bottom">Users</h2>

        <b-input-group prepend="Username">
            <b-form-input v-model="show_user_username"> </b-form-input>
            <b-input-group-append>
                <b-button
                    variant="outline-primary"
                    @click="clickShowUser"
                    :disabled="!show_user_username"
                >
                    Show
                </b-button>
            </b-input-group-append>
        </b-input-group>

        <b-modal
            id="new-admin-modal"
            title="New Admin"
            ok-title="Yes"
            cancel-title="No"
            @ok.prevent="clickOkNewAdminModal"
        >
            Are you sure you want to add new admin?
        </b-modal>
        <b-modal
            id="show-user-modal"
            :title="(show_user_blocked ? 'Unblock' : 'Block') + ' User'"
            :ok-title="show_user_blocked ? 'Unblock' : 'Block'"
            @ok.prevent="clickOkShowUserModal"
        >
            <span v-if="show_user_blocked">
                User @{{ this.show_user_username }} is currently blocked.
            </span>
            <span v-else>
                You can block user @{{ this.show_user_username }} if he/she
                violated our rules.
            </span>
        </b-modal>

        <b-modal title="Info" size="sm" id="info-modal" ok-only>
            {{ this.info }}
        </b-modal>
    </div>
</template>

<script>
import { BIconPlus } from "bootstrap-vue";
import {
    ADMIN_ADMINS_FETCH,
    ADMIN_ADMIN_TOGGLE,
    ADMIN_USER_FETCH,
    ADMIN_USER_BLOCK_TOGGLE,
    AUTH_SUPERADMIN,
} from "@/store/interface";
import User from "@/models/User";
import Vue from "vue";

export default {
    data() {
        return {
            admins: [],
            new_admin_username: "",
            new_admin_id: null,
            show_user_username: "",
            show_user_id: null,
            show_user_blocked: false,
            info: "",
        };
    },
    components: {
        IconAdd: BIconPlus,
    },
    created() {
        this.$store
            .dispatch(ADMIN_ADMINS_FETCH)
            .then(({ data }) => {
                this.admins = data.map((d) => d.id).map((id) => User.find(id));
            })
            .catch((err) => console.error(err));
    },
    computed: {
        adminsTableActionsColumnSpan() {
            return this.$store.getters[AUTH_SUPERADMIN] ? 2 : 1;
        },
        renderToggleAdminAction() {
            return this.$store.getters[AUTH_SUPERADMIN];
        },
        renderAddAdminRow() {
            return this.$store.getters[AUTH_SUPERADMIN];
        },
    },
    methods: {
        clickToggleAdmin(admin) {
            this.$store.dispatch(ADMIN_ADMIN_TOGGLE, admin.id).then(() => {
                Vue.set(
                    this.admins,
                    this.admins.indexOf(admin),
                    User.find(admin.id)
                );
            });
        },
        clickProfileAction(admin) {
            this.$router.push({
                name: "user_posts",
                params: { username: admin.username },
            });
        },
        clickAddNewAdmin() {
            if (!this.new_admin_username.length) return;

            this.$store
                .dispatch(ADMIN_USER_FETCH, this.new_admin_username)
                .then(({ data }) => {
                    if (data.admin) {
                        this.info = `User @${this.new_admin_username} is already admin.`;
                        this.$bvModal.show("info-modal");
                    } else if (data.blocked) {
                        this.info = `User @${this.new_admin_username} is blocked.`;
                        this.$bvModal.show("info-modal");
                    } else {
                        this.new_admin_id = data.id;
                        this.$bvModal.show("new-admin-modal");
                    }
                })
                .catch((err) => {
                    if (err.response.status == 404) {
                        this.info = `No such user @${this.new_admin_username}.`;
                        this.$bvModal.show("info-modal");
                    } else {
                        console.error(err);
                    }
                });
        },
        clickOkNewAdminModal() {
            this.$store
                .dispatch(ADMIN_ADMIN_TOGGLE, this.new_admin_id)
                .then(({ data }) => {
                    this.new_admin_username = "";
                    this.new_admin_id = null;
                    this.admins.push(User.find(data.id));
                    this.$bvModal.hide("new-admin-modal");
                })
                .catch((err) => console.error(err));
        },
        clickShowUser() {
            if (!this.show_user_username) return;

            this.$store
                .dispatch(ADMIN_USER_FETCH, this.show_user_username)
                .then(({ data }) => {
                    if (data.admin) {
                        this.info = `User @${this.show_user_username} is an admin.`;
                        this.$bvModal.show("info-modal");
                    } else {
                        this.show_user_id = data.id;
                        this.show_user_blocked = data.blocked;
                        this.$bvModal.show("show-user-modal");
                    }
                })
                .catch((err) => {
                    if (err.response.status == 404) {
                        this.info = `No such user @${this.show_user_username}.`;
                        this.$bvModal.show("info-modal");
                    } else {
                        console.error(err);
                    }
                });
        },
        clickOkShowUserModal() {
            this.$store
                .dispatch(ADMIN_USER_BLOCK_TOGGLE, this.show_user_id)
                .then(({ data }) => {
                    this.show_user_username = "";
                    this.show_user_id = null;
                    this.show_user_blocked = false;
                    this.$bvModal.hide("show-user-modal");
                })
                .catch((err) => console.error(err));
        },
    },
};
</script>

<style>
</style>