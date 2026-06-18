import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import crashTests from './crash-tests'
import testDrives from './test-drives'
import reviews from './reviews'
import configurationGroups from './configuration-groups'
import configurations from './configurations'
import equipmentCategories from './equipment-categories'
import equipment from './equipment'
import photoGroups from './photo-groups'
import photos from './photos'
/**
* @see \App\Http\Controllers\Admin\CarController::index
* @see app/Http/Controllers/Admin/CarController.php:23
* @route '/admin/cars'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarController::index
* @see app/Http/Controllers/Admin/CarController.php:23
* @route '/admin/cars'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::index
* @see app/Http/Controllers/Admin/CarController.php:23
* @route '/admin/cars'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarController::index
* @see app/Http/Controllers/Admin/CarController.php:23
* @route '/admin/cars'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarController::create
* @see app/Http/Controllers/Admin/CarController.php:75
* @route '/admin/cars/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarController::create
* @see app/Http/Controllers/Admin/CarController.php:75
* @route '/admin/cars/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::create
* @see app/Http/Controllers/Admin/CarController.php:75
* @route '/admin/cars/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarController::create
* @see app/Http/Controllers/Admin/CarController.php:75
* @route '/admin/cars/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarController::store
* @see app/Http/Controllers/Admin/CarController.php:87
* @route '/admin/cars'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarController::store
* @see app/Http/Controllers/Admin/CarController.php:87
* @route '/admin/cars'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::store
* @see app/Http/Controllers/Admin/CarController.php:87
* @route '/admin/cars'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarController::edit
* @see app/Http/Controllers/Admin/CarController.php:99
* @route '/admin/cars/{car}/edit'
*/
export const edit = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarController::edit
* @see app/Http/Controllers/Admin/CarController.php:99
* @route '/admin/cars/{car}/edit'
*/
edit.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::edit
* @see app/Http/Controllers/Admin/CarController.php:99
* @route '/admin/cars/{car}/edit'
*/
edit.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarController::edit
* @see app/Http/Controllers/Admin/CarController.php:99
* @route '/admin/cars/{car}/edit'
*/
edit.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarController::update
* @see app/Http/Controllers/Admin/CarController.php:136
* @route '/admin/cars/{car}'
*/
export const update = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarController::update
* @see app/Http/Controllers/Admin/CarController.php:136
* @route '/admin/cars/{car}'
*/
update.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::update
* @see app/Http/Controllers/Admin/CarController.php:136
* @route '/admin/cars/{car}'
*/
update.put = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarController::update
* @see app/Http/Controllers/Admin/CarController.php:136
* @route '/admin/cars/{car}'
*/
update.patch = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarController::destroy
* @see app/Http/Controllers/Admin/CarController.php:148
* @route '/admin/cars/{car}'
*/
export const destroy = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarController::destroy
* @see app/Http/Controllers/Admin/CarController.php:148
* @route '/admin/cars/{car}'
*/
destroy.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarController::destroy
* @see app/Http/Controllers/Admin/CarController.php:148
* @route '/admin/cars/{car}'
*/
destroy.delete = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const cars = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    crashTests: Object.assign(crashTests, crashTests),
    testDrives: Object.assign(testDrives, testDrives),
    reviews: Object.assign(reviews, reviews),
    configurationGroups: Object.assign(configurationGroups, configurationGroups),
    configurations: Object.assign(configurations, configurations),
    equipmentCategories: Object.assign(equipmentCategories, equipmentCategories),
    equipment: Object.assign(equipment, equipment),
    photoGroups: Object.assign(photoGroups, photoGroups),
    photos: Object.assign(photos, photos),
}

export default cars