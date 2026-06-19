import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
const ElectricCarController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ElectricCarController.url(options),
    method: 'get',
})

ElectricCarController.definition = {
    methods: ["get","head"],
    url: '/electric-cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
ElectricCarController.url = (options?: RouteQueryOptions) => {
    return ElectricCarController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
ElectricCarController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: ElectricCarController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
ElectricCarController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: ElectricCarController.url(options),
    method: 'head',
})

export default ElectricCarController