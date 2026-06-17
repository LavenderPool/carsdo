import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:23
* @route '/admin/danger/full-clear'
*/
export const fullClear = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fullClear.url(options),
    method: 'get',
})

fullClear.definition = {
    methods: ["get","head"],
    url: '/admin/danger/full-clear',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:23
* @route '/admin/danger/full-clear'
*/
fullClear.url = (options?: RouteQueryOptions) => {
    return fullClear.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:23
* @route '/admin/danger/full-clear'
*/
fullClear.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fullClear.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:23
* @route '/admin/danger/full-clear'
*/
fullClear.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fullClear.url(options),
    method: 'head',
})

const danger = {
    fullClear: Object.assign(fullClear, fullClear),
}

export default danger