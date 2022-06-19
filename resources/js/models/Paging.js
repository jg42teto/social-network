import { Model } from '@vuex-orm/core'

class Paging extends Model {
    static entity = 'paging';

    static fields() {
        return {
            id: this.string(),
            next_page: this.string().nullable(),
            items: this.attr([]),
        }
    }
}

export default Paging;