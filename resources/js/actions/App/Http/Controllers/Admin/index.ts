import DashboardController from './DashboardController'
import BrandController from './BrandController'
import CarController from './CarController'
import CarCrashTestController from './CarCrashTestController'
import CarTestDriveController from './CarTestDriveController'
import CarReviewController from './CarReviewController'
import CarConfigurationGroupController from './CarConfigurationGroupController'
import CarConfigurationController from './CarConfigurationController'
import CarConfigurationEquipmentCategoryController from './CarConfigurationEquipmentCategoryController'
import CarConfigurationEquipmentController from './CarConfigurationEquipmentController'
import CarPhotoGroupController from './CarPhotoGroupController'
import CarPhotoController from './CarPhotoController'
import ImportController from './ImportController'
import DangerController from './DangerController'
import SettingController from './SettingController'

const Admin = {
    DashboardController: Object.assign(DashboardController, DashboardController),
    BrandController: Object.assign(BrandController, BrandController),
    CarController: Object.assign(CarController, CarController),
    CarCrashTestController: Object.assign(CarCrashTestController, CarCrashTestController),
    CarTestDriveController: Object.assign(CarTestDriveController, CarTestDriveController),
    CarReviewController: Object.assign(CarReviewController, CarReviewController),
    CarConfigurationGroupController: Object.assign(CarConfigurationGroupController, CarConfigurationGroupController),
    CarConfigurationController: Object.assign(CarConfigurationController, CarConfigurationController),
    CarConfigurationEquipmentCategoryController: Object.assign(CarConfigurationEquipmentCategoryController, CarConfigurationEquipmentCategoryController),
    CarConfigurationEquipmentController: Object.assign(CarConfigurationEquipmentController, CarConfigurationEquipmentController),
    CarPhotoGroupController: Object.assign(CarPhotoGroupController, CarPhotoGroupController),
    CarPhotoController: Object.assign(CarPhotoController, CarPhotoController),
    ImportController: Object.assign(ImportController, ImportController),
    DangerController: Object.assign(DangerController, DangerController),
    SettingController: Object.assign(SettingController, SettingController),
}

export default Admin