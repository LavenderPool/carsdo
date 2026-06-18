import SitemapController from './SitemapController'
import Admin from './Admin'
import ProfileController from './ProfileController'
import Auth from './Auth'

const Controllers = {
    SitemapController: Object.assign(SitemapController, SitemapController),
    Admin: Object.assign(Admin, Admin),
    ProfileController: Object.assign(ProfileController, ProfileController),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers