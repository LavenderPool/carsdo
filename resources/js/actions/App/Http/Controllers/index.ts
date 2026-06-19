import Admin from './Admin'
import ProfileController from './ProfileController'
import Auth from './Auth'
import Site from './Site'
import SitemapController from './SitemapController'

const Controllers = {
    Admin: Object.assign(Admin, Admin),
    ProfileController: Object.assign(ProfileController, ProfileController),
    Auth: Object.assign(Auth, Auth),
    Site: Object.assign(Site, Site),
    SitemapController: Object.assign(SitemapController, SitemapController),
}

export default Controllers