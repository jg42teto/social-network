import { Model } from '@vuex-orm/core'
import User from './User'

class Post extends Model {
    static entity = 'posts';

    static fields() {
        return {
            id: this.number(),
            user_id: this.number().nullable(),
            parent_post_id: this.attr().nullable(),
            shared_post_id: this.attr().nullable(),
            content: this.string(''),
            mentioned_users: this.string(''),
            comments_number: this.number(),
            likes_number: this.number(),
            shares_number: this.number(),
            created_at: this.string(),
            updated_at: this.string(),
            user: this.belongsTo(User, 'user_id'),
            parent_post: this.belongsTo(Post, 'parent_post_id'),
            shared_post: this.belongsTo(Post, 'shared_post_id'),
            child_posts: this.hasMany(Post, 'parent_post_id'),
            liked: this.boolean(),
            shared: this.boolean(),
        }
    }
}

export default Post;