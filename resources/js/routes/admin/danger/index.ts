import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import setLocalIdsCce7c0 from './set-local-ids'
/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:91
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
* @see app/Http/Controllers/Admin/DangerController.php:91
* @route '/admin/danger/full-clear'
*/
fullClear.url = (options?: RouteQueryOptions) => {
    return fullClear.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:91
* @route '/admin/danger/full-clear'
*/
fullClear.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fullClear.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:91
* @route '/admin/danger/full-clear'
*/
fullClear.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fullClear.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:30
* @route '/admin/danger/set-local-ids'
*/
export const setLocalIds = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setLocalIds.url(options),
    method: 'get',
})

setLocalIds.definition = {
    methods: ["get","head"],
    url: '/admin/danger/set-local-ids',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:30
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.url = (options?: RouteQueryOptions) => {
    return setLocalIds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:30
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setLocalIds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:30
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setLocalIds.url(options),
    method: 'head',
})

const danger = {
    fullClear: Object.assign(fullClear, fullClear),
    setLocalIds: Object.assign(setLocalIds, setLocalIdsCce7c0),
}

export default danger