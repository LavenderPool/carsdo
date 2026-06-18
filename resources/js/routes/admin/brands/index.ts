import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\BrandController::index
* @see app/Http/Controllers/Admin/BrandController.php:20
* @route '/admin/brands'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/brands',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::index
* @see app/Http/Controllers/Admin/BrandController.php:20
* @route '/admin/brands'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::index
* @see app/Http/Controllers/Admin/BrandController.php:20
* @route '/admin/brands'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::index
* @see app/Http/Controllers/Admin/BrandController.php:20
* @route '/admin/brands'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::create
* @see app/Http/Controllers/Admin/BrandController.php:53
* @route '/admin/brands/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/brands/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::create
* @see app/Http/Controllers/Admin/BrandController.php:53
* @route '/admin/brands/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::create
* @see app/Http/Controllers/Admin/BrandController.php:53
* @route '/admin/brands/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::create
* @see app/Http/Controllers/Admin/BrandController.php:53
* @route '/admin/brands/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::store
* @see app/Http/Controllers/Admin/BrandController.php:61
* @route '/admin/brands'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/brands',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::store
* @see app/Http/Controllers/Admin/BrandController.php:61
* @route '/admin/brands'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::store
* @see app/Http/Controllers/Admin/BrandController.php:61
* @route '/admin/brands'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::edit
* @see app/Http/Controllers/Admin/BrandController.php:73
* @route '/admin/brands/{brand}/edit'
*/
export const edit = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/brands/{brand}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::edit
* @see app/Http/Controllers/Admin/BrandController.php:73
* @route '/admin/brands/{brand}/edit'
*/
edit.url = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { brand: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { brand: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            brand: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.id
        : args.brand,
    }

    return edit.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::edit
* @see app/Http/Controllers/Admin/BrandController.php:73
* @route '/admin/brands/{brand}/edit'
*/
edit.get = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::edit
* @see app/Http/Controllers/Admin/BrandController.php:73
* @route '/admin/brands/{brand}/edit'
*/
edit.head = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::update
* @see app/Http/Controllers/Admin/BrandController.php:88
* @route '/admin/brands/{brand}'
*/
export const update = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/brands/{brand}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::update
* @see app/Http/Controllers/Admin/BrandController.php:88
* @route '/admin/brands/{brand}'
*/
update.url = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { brand: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { brand: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            brand: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.id
        : args.brand,
    }

    return update.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::update
* @see app/Http/Controllers/Admin/BrandController.php:88
* @route '/admin/brands/{brand}'
*/
update.put = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::update
* @see app/Http/Controllers/Admin/BrandController.php:88
* @route '/admin/brands/{brand}'
*/
update.patch = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\BrandController::destroy
* @see app/Http/Controllers/Admin/BrandController.php:100
* @route '/admin/brands/{brand}'
*/
export const destroy = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/brands/{brand}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\BrandController::destroy
* @see app/Http/Controllers/Admin/BrandController.php:100
* @route '/admin/brands/{brand}'
*/
destroy.url = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { brand: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { brand: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            brand: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.id
        : args.brand,
    }

    return destroy.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\BrandController::destroy
* @see app/Http/Controllers/Admin/BrandController.php:100
* @route '/admin/brands/{brand}'
*/
destroy.delete = (args: { brand: string | number | { id: string | number } } | [brand: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const brands = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default brands