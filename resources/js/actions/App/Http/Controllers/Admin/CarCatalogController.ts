import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarCatalogController::index
* @see app/Http/Controllers/Admin/CarCatalogController.php:19
* @route '/admin/car-catalogs'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/car-catalogs',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::index
* @see app/Http/Controllers/Admin/CarCatalogController.php:19
* @route '/admin/car-catalogs'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::index
* @see app/Http/Controllers/Admin/CarCatalogController.php:19
* @route '/admin/car-catalogs'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::index
* @see app/Http/Controllers/Admin/CarCatalogController.php:19
* @route '/admin/car-catalogs'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::create
* @see app/Http/Controllers/Admin/CarCatalogController.php:62
* @route '/admin/car-catalogs/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/car-catalogs/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::create
* @see app/Http/Controllers/Admin/CarCatalogController.php:62
* @route '/admin/car-catalogs/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::create
* @see app/Http/Controllers/Admin/CarCatalogController.php:62
* @route '/admin/car-catalogs/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::create
* @see app/Http/Controllers/Admin/CarCatalogController.php:62
* @route '/admin/car-catalogs/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::store
* @see app/Http/Controllers/Admin/CarCatalogController.php:69
* @route '/admin/car-catalogs'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/car-catalogs',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::store
* @see app/Http/Controllers/Admin/CarCatalogController.php:69
* @route '/admin/car-catalogs'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::store
* @see app/Http/Controllers/Admin/CarCatalogController.php:69
* @route '/admin/car-catalogs'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::edit
* @see app/Http/Controllers/Admin/CarCatalogController.php:95
* @route '/admin/car-catalogs/{car_catalog}/edit'
*/
export const edit = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/car-catalogs/{car_catalog}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::edit
* @see app/Http/Controllers/Admin/CarCatalogController.php:95
* @route '/admin/car-catalogs/{car_catalog}/edit'
*/
edit.url = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_catalog: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_catalog: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_catalog: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_catalog: typeof args.car_catalog === 'object'
        ? args.car_catalog.id
        : args.car_catalog,
    }

    return edit.definition.url
            .replace('{car_catalog}', parsedArgs.car_catalog.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::edit
* @see app/Http/Controllers/Admin/CarCatalogController.php:95
* @route '/admin/car-catalogs/{car_catalog}/edit'
*/
edit.get = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::edit
* @see app/Http/Controllers/Admin/CarCatalogController.php:95
* @route '/admin/car-catalogs/{car_catalog}/edit'
*/
edit.head = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::update
* @see app/Http/Controllers/Admin/CarCatalogController.php:138
* @route '/admin/car-catalogs/{car_catalog}'
*/
export const update = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/car-catalogs/{car_catalog}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::update
* @see app/Http/Controllers/Admin/CarCatalogController.php:138
* @route '/admin/car-catalogs/{car_catalog}'
*/
update.url = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_catalog: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_catalog: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_catalog: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_catalog: typeof args.car_catalog === 'object'
        ? args.car_catalog.id
        : args.car_catalog,
    }

    return update.definition.url
            .replace('{car_catalog}', parsedArgs.car_catalog.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::update
* @see app/Http/Controllers/Admin/CarCatalogController.php:138
* @route '/admin/car-catalogs/{car_catalog}'
*/
update.put = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::update
* @see app/Http/Controllers/Admin/CarCatalogController.php:138
* @route '/admin/car-catalogs/{car_catalog}'
*/
update.patch = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::destroy
* @see app/Http/Controllers/Admin/CarCatalogController.php:164
* @route '/admin/car-catalogs/{car_catalog}'
*/
export const destroy = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/car-catalogs/{car_catalog}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::destroy
* @see app/Http/Controllers/Admin/CarCatalogController.php:164
* @route '/admin/car-catalogs/{car_catalog}'
*/
destroy.url = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_catalog: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_catalog: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_catalog: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_catalog: typeof args.car_catalog === 'object'
        ? args.car_catalog.id
        : args.car_catalog,
    }

    return destroy.definition.url
            .replace('{car_catalog}', parsedArgs.car_catalog.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCatalogController::destroy
* @see app/Http/Controllers/Admin/CarCatalogController.php:164
* @route '/admin/car-catalogs/{car_catalog}'
*/
destroy.delete = (args: { car_catalog: number | { id: number } } | [car_catalog: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarCatalogController = { index, create, store, edit, update, destroy }

export default CarCatalogController