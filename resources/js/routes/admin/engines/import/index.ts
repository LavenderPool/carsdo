import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\EngineImportController::index
* @see app/Http/Controllers/Admin/EngineImportController.php:18
* @route '/admin/engines/import'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/engines/import',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EngineImportController::index
* @see app/Http/Controllers/Admin/EngineImportController.php:18
* @route '/admin/engines/import'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineImportController::index
* @see app/Http/Controllers/Admin/EngineImportController.php:18
* @route '/admin/engines/import'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EngineImportController::index
* @see app/Http/Controllers/Admin/EngineImportController.php:18
* @route '/admin/engines/import'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\EngineImportController::store
* @see app/Http/Controllers/Admin/EngineImportController.php:31
* @route '/admin/engines/import'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/engines/import',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\EngineImportController::store
* @see app/Http/Controllers/Admin/EngineImportController.php:31
* @route '/admin/engines/import'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineImportController::store
* @see app/Http/Controllers/Admin/EngineImportController.php:31
* @route '/admin/engines/import'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\EngineImportController::status
* @see app/Http/Controllers/Admin/EngineImportController.php:55
* @route '/admin/engines/import/{engineImportRun}'
*/
export const status = (args: { engineImportRun: number | { id: number } } | [engineImportRun: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

status.definition = {
    methods: ["get","head"],
    url: '/admin/engines/import/{engineImportRun}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EngineImportController::status
* @see app/Http/Controllers/Admin/EngineImportController.php:55
* @route '/admin/engines/import/{engineImportRun}'
*/
status.url = (args: { engineImportRun: number | { id: number } } | [engineImportRun: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { engineImportRun: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { engineImportRun: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            engineImportRun: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        engineImportRun: typeof args.engineImportRun === 'object'
        ? args.engineImportRun.id
        : args.engineImportRun,
    }

    return status.definition.url
            .replace('{engineImportRun}', parsedArgs.engineImportRun.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineImportController::status
* @see app/Http/Controllers/Admin/EngineImportController.php:55
* @route '/admin/engines/import/{engineImportRun}'
*/
status.get = (args: { engineImportRun: number | { id: number } } | [engineImportRun: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: status.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EngineImportController::status
* @see app/Http/Controllers/Admin/EngineImportController.php:55
* @route '/admin/engines/import/{engineImportRun}'
*/
status.head = (args: { engineImportRun: number | { id: number } } | [engineImportRun: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: status.url(args, options),
    method: 'head',
})

const importMethod = {
    index: Object.assign(index, index),
    store: Object.assign(store, store),
    status: Object.assign(status, status),
}

export default importMethod