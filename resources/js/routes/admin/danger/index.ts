import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../wayfinder'
import setLocalIdsCce7c0 from './set-local-ids'
import webpConvertA429b2 from './webp-convert'
import convert22b4d6 from './convert'
/**
* @see \App\Http\Controllers\Admin\DangerController::clearCache
* @see app/Http/Controllers/Admin/DangerController.php:36
* @route '/admin/danger/clear-cache'
*/
export const clearCache = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clearCache.url(options),
    method: 'post',
})

clearCache.definition = {
    methods: ["post"],
    url: '/admin/danger/clear-cache',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::clearCache
* @see app/Http/Controllers/Admin/DangerController.php:36
* @route '/admin/danger/clear-cache'
*/
clearCache.url = (options?: RouteQueryOptions) => {
    return clearCache.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::clearCache
* @see app/Http/Controllers/Admin/DangerController.php:36
* @route '/admin/danger/clear-cache'
*/
clearCache.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: clearCache.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:179
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
* @see app/Http/Controllers/Admin/DangerController.php:179
* @route '/admin/danger/full-clear'
*/
fullClear.url = (options?: RouteQueryOptions) => {
    return fullClear.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:179
* @route '/admin/danger/full-clear'
*/
fullClear.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: fullClear.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::fullClear
* @see app/Http/Controllers/Admin/DangerController.php:179
* @route '/admin/danger/full-clear'
*/
fullClear.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: fullClear.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:45
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
* @see app/Http/Controllers/Admin/DangerController.php:45
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.url = (options?: RouteQueryOptions) => {
    return setLocalIds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:45
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: setLocalIds.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::setLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:45
* @route '/admin/danger/set-local-ids'
*/
setLocalIds.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: setLocalIds.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::webpConvert
* @see app/Http/Controllers/Admin/DangerController.php:104
* @route '/admin/danger/webp-convert'
*/
export const webpConvert = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webpConvert.url(options),
    method: 'get',
})

webpConvert.definition = {
    methods: ["get","head"],
    url: '/admin/danger/webp-convert',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::webpConvert
* @see app/Http/Controllers/Admin/DangerController.php:104
* @route '/admin/danger/webp-convert'
*/
webpConvert.url = (options?: RouteQueryOptions) => {
    return webpConvert.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::webpConvert
* @see app/Http/Controllers/Admin/DangerController.php:104
* @route '/admin/danger/webp-convert'
*/
webpConvert.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: webpConvert.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::webpConvert
* @see app/Http/Controllers/Admin/DangerController.php:104
* @route '/admin/danger/webp-convert'
*/
webpConvert.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: webpConvert.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::convert
* @see app/Http/Controllers/Admin/DangerController.php:141
* @route '/admin/danger/convert'
*/
export const convert = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: convert.url(options),
    method: 'get',
})

convert.definition = {
    methods: ["get","head"],
    url: '/admin/danger/convert',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::convert
* @see app/Http/Controllers/Admin/DangerController.php:141
* @route '/admin/danger/convert'
*/
convert.url = (options?: RouteQueryOptions) => {
    return convert.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::convert
* @see app/Http/Controllers/Admin/DangerController.php:141
* @route '/admin/danger/convert'
*/
convert.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: convert.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DangerController::convert
* @see app/Http/Controllers/Admin/DangerController.php:141
* @route '/admin/danger/convert'
*/
convert.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: convert.url(options),
    method: 'head',
})

const danger = {
    clearCache: Object.assign(clearCache, clearCache),
    fullClear: Object.assign(fullClear, fullClear),
    setLocalIds: Object.assign(setLocalIds, setLocalIdsCce7c0),
    webpConvert: Object.assign(webpConvert, webpConvertA429b2),
    convert: Object.assign(convert, convert22b4d6),
}

export default danger