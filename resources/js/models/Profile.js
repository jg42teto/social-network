import { Model } from '@vuex-orm/core'

class Profile extends Model {
    static entity = 'profiles';

    static fields() {
        return {
            id: this.number(),
            user_id: this.number(),
            name: this.string(),
            bio: this.string(),
            avatar: this.string().nullable(),
            following_number: this.number(0),
            followers_number: this.number(0),
        }
    }
}

export default Profile;