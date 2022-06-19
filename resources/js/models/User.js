import { Model } from '@vuex-orm/core'
import Post from './Post'
import Profile from './Profile'

class User extends Model {
    static entity = 'users';

    static fields() {
        return {
            id: this.number(),
            username: this.string(),
            email: this.string(),
            admin: this.number(),
            blocked: this.boolean(),
            created_at: this.string(),
            following: this.boolean(false),
            profile: this.hasOne(Profile, 'user_id'),
            posts: this.hasMany(Post, 'user_id'),
        }
    }
}

export default User;