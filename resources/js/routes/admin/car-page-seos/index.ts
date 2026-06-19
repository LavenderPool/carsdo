import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::index
* @see app/Http/Controllers/Admin/CarPageSeoController.php:16
* @route '/admin/car-page-seos'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/car-page-seos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::index
* @see app/Http/Controllers/Admin/CarPageSeoController.php:16
* @route '/admin/car-page-seos'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::index
* @see app/Http/Controllers/Admin/CarPageSeoController.php:16
* @route '/admin/car-page-seos'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::index
* @see app/Http/Controllers/Admin/CarPageSeoController.php:16
* @route '/admin/car-page-seos'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::edit
* @see app/Http/Controllers/Admin/CarPageSeoController.php:45
* @route '/admin/car-page-seos/{pageKey}'
*/
export const edit = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/car-page-seos/{pageKey}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::edit
* @see app/Http/Controllers/Admin/CarPageSeoController.php:45
* @route '/admin/car-page-seos/{pageKey}'
*/
edit.url = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { pageKey: args }
    }

    if (Array.isArray(args)) {
        args = {
            pageKey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        pageKey: args.pageKey,
    }

    return edit.definition.url
            .replace('{pageKey}', parsedArgs.pageKey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::edit
* @see app/Http/Controllers/Admin/CarPageSeoController.php:45
* @route '/admin/car-page-seos/{pageKey}'
*/
edit.get = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::edit
* @see app/Http/Controllers/Admin/CarPageSeoController.php:45
* @route '/admin/car-page-seos/{pageKey}'
*/
edit.head = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::update
* @see app/Http/Controllers/Admin/CarPageSeoController.php:62
* @route '/admin/car-page-seos/{pageKey}'
*/
export const update = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/admin/car-page-seos/{pageKey}',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::update
* @see app/Http/Controllers/Admin/CarPageSeoController.php:62
* @route '/admin/car-page-seos/{pageKey}'
*/
update.url = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { pageKey: args }
    }

    if (Array.isArray(args)) {
        args = {
            pageKey: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        pageKey: args.pageKey,
    }

    return update.definition.url
            .replace('{pageKey}', parsedArgs.pageKey.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPageSeoController::update
* @see app/Http/Controllers/Admin/CarPageSeoController.php:62
* @route '/admin/car-page-seos/{pageKey}'
*/
update.put = (args: { pageKey: string | number } | [pageKey: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

const carPageSeos = {
    index: Object.assign(index, index),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
}

export default carPageSeos