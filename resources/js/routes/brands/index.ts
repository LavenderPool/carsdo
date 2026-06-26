import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Site\BrandController::index
* @see app/Http/Controllers/Site/BrandController.php:14
* @route '/brands'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/brands',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\BrandController::index
* @see app/Http/Controllers/Site/BrandController.php:14
* @route '/brands'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\BrandController::index
* @see app/Http/Controllers/Site/BrandController.php:14
* @route '/brands'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\BrandController::index
* @see app/Http/Controllers/Site/BrandController.php:14
* @route '/brands'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

const brands = {
    index: Object.assign(index, index),
}

export default brands