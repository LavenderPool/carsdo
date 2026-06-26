import HomeController from './HomeController'
import CoverController from './CoverController'
import CrashTestController from './CrashTestController'
import TestDriveController from './TestDriveController'
import NewCarController from './NewCarController'
import ElectricCarController from './ElectricCarController'
import PopularCarController from './PopularCarController'
import BlogController from './BlogController'
import PageController from './PageController'
import CarPhotoGalleryController from './CarPhotoGalleryController'
import BrandController from './BrandController'
import SearchController from './SearchController'
import CatalogController from './CatalogController'
import CarController from './CarController'

const Site = {
    HomeController: Object.assign(HomeController, HomeController),
    CoverController: Object.assign(CoverController, CoverController),
    CrashTestController: Object.assign(CrashTestController, CrashTestController),
    TestDriveController: Object.assign(TestDriveController, TestDriveController),
    NewCarController: Object.assign(NewCarController, NewCarController),
    ElectricCarController: Object.assign(ElectricCarController, ElectricCarController),
    PopularCarController: Object.assign(PopularCarController, PopularCarController),
    BlogController: Object.assign(BlogController, BlogController),
    PageController: Object.assign(PageController, PageController),
    CarPhotoGalleryController: Object.assign(CarPhotoGalleryController, CarPhotoGalleryController),
    BrandController: Object.assign(BrandController, BrandController),
    SearchController: Object.assign(SearchController, SearchController),
    CatalogController: Object.assign(CatalogController, CatalogController),
    CarController: Object.assign(CarController, CarController),
}

export default Site