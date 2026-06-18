import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\ImportController::index
* @see app/Http/Controllers/Admin/ImportController.php:19
* @route '/admin/import'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/import',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ImportController::index
* @see app/Http/Controllers/Admin/ImportController.php:19
* @route '/admin/import'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ImportController::index
* @see app/Http/Controllers/Admin/ImportController.php:19
* @route '/admin/import'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ImportController::index
* @see app/Http/Controllers/Admin/ImportController.php:19
* @route '/admin/import'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ImportController::store
* @see app/Http/Controllers/Admin/ImportController.php:33
* @route '/admin/import'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\ImportController::store
* @see app/Http/Controllers/Admin/ImportController.php:33
* @route '/admin/import'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ImportController::store
* @see app/Http/Controllers/Admin/ImportController.php:33
* @route '/admin/import'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\ImportController::status
* @see app/Http/Controllers/Admin/ImportController.php:68
* @route '/admin/import/{importRun}'
*/
export const status = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/admin/import/{importRun}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ImportController::status
* @see app/Http/Controllers/Admin/ImportController.php:68
* @route '/admin/import/{importRun}'
*/
status.url = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { importRun: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { importRun: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            importRun: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        importRun: typeof args.importRun === 'object'
        ? args.importRun.id
        : args.importRun,
    }

    return status.definition.url
            .replace('{importRun}', parsedArgs.importRun.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ImportController::status
* @see app/Http/Controllers/Admin/ImportController.php:68
* @route '/admin/import/{importRun}'
*/
status.get = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ImportController::status
* @see app/Http/Controllers/Admin/ImportController.php:68
* @route '/admin/import/{importRun}'
*/
status.head = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ImportController::stop
* @see app/Http/Controllers/Admin/ImportController.php:77
* @route '/admin/import/{importRun}/stop'
*/
export const stop = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

stop.definition = {
    methods: ["post"],
    url: '/admin/import/{importRun}/stop',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\ImportController::stop
* @see app/Http/Controllers/Admin/ImportController.php:77
* @route '/admin/import/{importRun}/stop'
*/
stop.url = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { importRun: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { importRun: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            importRun: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        importRun: typeof args.importRun === 'object'
        ? args.importRun.id
        : args.importRun,
    }

    return stop.definition.url
            .replace('{importRun}', parsedArgs.importRun.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ImportController::stop
* @see app/Http/Controllers/Admin/ImportController.php:77
* @route '/admin/import/{importRun}/stop'
*/
stop.post = (args: { importRun: string | number | { id: string | number } } | [importRun: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: stop.url(args, options),
    method: 'post',
})

const importMethod = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    status: Object.assign(status, status),
    stop: Object.assign(stop, stop),
}

export default importMethod