import { Model } from '@vuex-orm/core'
import User from './User'
import Post from './Post'

class MetaPost extends Model {
    static entity = 'meta_posts';

    static fields() {
        return {
            id: this.number(),
            post_id: this.number(),
            user_id: this.number(),
            repost: this.boolean(),
            post: this.belongsTo(Post, 'post_id'),
            user: this.belongsTo(User, 'user_id'),
        }
    }
}

export default MetaPost;