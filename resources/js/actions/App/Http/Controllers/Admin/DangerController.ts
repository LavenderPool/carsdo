import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
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
* @see \App\Http\Controllers\Admin\DangerController::applySetLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
export const applySetLocalIds = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applySetLocalIds.url(options),
    method: 'post',
})

applySetLocalIds.definition = {
    methods: ["post"],
    url: '/admin/danger/set-local-ids',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::applySetLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
applySetLocalIds.url = (options?: RouteQueryOptions) => {
    return applySetLocalIds.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::applySetLocalIds
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
applySetLocalIds.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applySetLocalIds.url(options),
    method: 'post',
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
* @see \App\Http\Controllers\Admin\DangerController::applyWebpConvert
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
export const applyWebpConvert = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyWebpConvert.url(options),
    method: 'post',
})

applyWebpConvert.definition = {
    methods: ["post"],
    url: '/admin/danger/webp-convert',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::applyWebpConvert
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
applyWebpConvert.url = (options?: RouteQueryOptions) => {
    return applyWebpConvert.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::applyWebpConvert
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
applyWebpConvert.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyWebpConvert.url(options),
    method: 'post',
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

/**
* @see \App\Http\Controllers\Admin\DangerController::applyConvert
* @see app/Http/Controllers/Admin/DangerController.php:149
* @route '/admin/danger/convert'
*/
export const applyConvert = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyConvert.url(options),
    method: 'post',
})

applyConvert.definition = {
    methods: ["post"],
    url: '/admin/danger/convert',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::applyConvert
* @see app/Http/Controllers/Admin/DangerController.php:149
* @route '/admin/danger/convert'
*/
applyConvert.url = (options?: RouteQueryOptions) => {
    return applyConvert.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::applyConvert
* @see app/Http/Controllers/Admin/DangerController.php:149
* @route '/admin/danger/convert'
*/
applyConvert.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: applyConvert.url(options),
    method: 'post',
})

const DangerController = { clearCache, fullClear, setLocalIds, applySetLocalIds, webpConvert, applyWebpConvert, convert, applyConvert }

export default DangerController