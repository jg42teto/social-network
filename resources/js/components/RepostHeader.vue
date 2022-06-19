<template>
    <div class="text-center">
        <div
            class="
                d-inline-block
                px-3
                border border-bottom-0 border-muted
                rounded-top
            "
        >
            <user-handle :user_id="user_id" /> reposted
        </div>
    </div>
</template>

<script>
import UserHandle from "./UserHandle.vue";

export default {
    components: { UserHandle },
    props: {
        user_id: { required: true },
    },
};
</script>

<style>
</style>