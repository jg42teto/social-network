<template>
    <div>
        <template v-for="part in parts"
            ><template v-if="part.type === 0">{{ part.text }}</template
            ><span v-if="part.type === 1" @click="clickHashtag"
                ><router-link
                    :to="{
                        name: 'hashtag_explorer',
                        params: { hashtag: part.text.substr(1) },
                    }"
                    >{{ part.text }}</router-link
                ></span
            ><span v-if="part.type === 2" @click="clickMention"
                ><router-link
                    :to="{
                        name: 'user_posts',
                        params: { username: part.text.substr(1) },
                    }"
                    >{{ part.text }}</router-link
                ></span
            ><br v-if="part.type === 3"
        /></template>
    </div>
</template>

<script>
import Post from "@/models/Post";

const HASHTAG_PATTERN =
    /(?<![a-zA-Z0-9_&])#[0-9_]*[a-zA-Z][a-zA-Z0-9_]*(?![#a-zA-Z0-9_])/g;

const MENTION_PATTERN = /(?<![a-zA-Z0-9_@#&])@[a-zA-Z0-9_]*(?![@a-zA-Z0-9_])/g;

export default {
    props: {
        post_id: {
            required: true,
        },
    },
    data() {
        return {
            parts: [],
        };
    },
    computed: {
        post() {
            return Post.query().find(this.post_id);
        },
    },
    mounted() {
        this.parse();
    },
    methods: {
        clickHashtag(event) {
            event.stopPropagation();
        },
        clickMention(event) {
            event.stopPropagation();
        },
        parse() {
            if (!this.post) return;
            let parts = [];
            let content = this.post.content;

            // capturing hashtag parts
            parts.push(
                ...[...content.matchAll(HASHTAG_PATTERN)].map((m) => ({
                    text: m[0],
                    index: m.index,
                    type: 1,
                }))
            );

            // capturing mention parts
            let valid_mentions = this.post.mentioned_users.split(",");
            let mentions = [...content.matchAll(MENTION_PATTERN)].map((m) => ({
                text: m[0],
                index: m.index,
                type: 2,
            }));
            mentions = mentions.filter(
                (mention) =>
                    valid_mentions.indexOf(mention.text.substr(1)) !== -1
            );
            parts.push(...mentions);

            // capturing newline parts
            parts.push(
                ...[...content.matchAll(/\n/g)].map((m) => ({
                    text: m[0],
                    index: m.index,
                    type: 3,
                }))
            );

            // capturing raw text parts
            parts.sort((a, b) => a.index - b.index);
            let raws = [];
            let index = 0;
            parts.forEach((part) => {
                if (part.index != index) {
                    raws.push({
                        text: content.substr(index, part.index - index),
                        index,
                        type: 0,
                    });
                }
                index = part.index + part.text.length;
            });
            if (index < content.length) {
                raws.push({
                    text: content.substr(index, content.length - index),
                    index,
                    type: 0,
                });
            }
            parts.push(...raws);

            // sort parts
            parts.sort((a, b) => a.index - b.index);
            this.parts = parts;
        },
    },
    watch: {
        post() {
            this.parse();
        },
    },
};
</script>

<style>
</style>