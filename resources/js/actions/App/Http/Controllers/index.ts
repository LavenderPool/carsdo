import Admin from './Admin'
import ProfileController from './ProfileController'
import Auth from './Auth'

const Controllers = {
    Admin: Object.assign(Admin, Admin),
    ProfileController: Object.assign(ProfileController, ProfileController),
    Auth: Object.assign(Auth, Auth),
}

export default Controllers