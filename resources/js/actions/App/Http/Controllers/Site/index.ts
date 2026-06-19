import HomeController from './HomeController'
import CoverController from './CoverController'
import CrashTestController from './CrashTestController'
import TestDriveController from './TestDriveController'
import NewCarController from './NewCarController'
import ElectricCarController from './ElectricCarController'
import CarPhotoGalleryController from './CarPhotoGalleryController'
import BrandController from './BrandController'
import CarController from './CarController'

const Site = {
    HomeController: Object.assign(HomeController, HomeController),
    CoverController: Object.assign(CoverController, CoverController),
    CrashTestController: Object.assign(CrashTestController, CrashTestController),
    TestDriveController: Object.assign(TestDriveController, TestDriveController),
    NewCarController: Object.assign(NewCarController, NewCarController),
    ElectricCarController: Object.assign(ElectricCarController, ElectricCarController),
    CarPhotoGalleryController: Object.assign(CarPhotoGalleryController, CarPhotoGalleryController),
    BrandController: Object.assign(BrandController, BrandController),
    CarController: Object.assign(CarController, CarController),
}

export default Site