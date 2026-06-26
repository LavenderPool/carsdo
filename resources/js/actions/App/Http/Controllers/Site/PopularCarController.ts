import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\PopularCarController::__invoke
* @see app/Http/Controllers/Site/PopularCarController.php:12
* @route '/popular-cars'
*/
const PopularCarController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PopularCarController.url(options),
    method: 'get',
})

PopularCarController.definition = {
    methods: ["get","head"],
    url: '/popular-cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PopularCarController::__invoke
* @see app/Http/Controllers/Site/PopularCarController.php:12
* @route '/popular-cars'
*/
PopularCarController.url = (options?: RouteQueryOptions) => {
    return PopularCarController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PopularCarController::__invoke
* @see app/Http/Controllers/Site/PopularCarController.php:12
* @route '/popular-cars'
*/
PopularCarController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: PopularCarController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PopularCarController::__invoke
* @see app/Http/Controllers/Site/PopularCarController.php:12
* @route '/popular-cars'
*/
PopularCarController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: PopularCarController.url(options),
    method: 'head',
})

export default PopularCarController