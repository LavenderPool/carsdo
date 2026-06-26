import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Site\SearchController::suggest
* @see app/Http/Controllers/Site/SearchController.php:61
* @route '/search/suggest'
*/
export const suggest = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggest.url(options),
    method: 'get',
})

suggest.definition = {
    methods: ["get","head"],
    url: '/search/suggest',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\SearchController::suggest
* @see app/Http/Controllers/Site/SearchController.php:61
* @route '/search/suggest'
*/
suggest.url = (options?: RouteQueryOptions) => {
    return suggest.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\SearchController::suggest
* @see app/Http/Controllers/Site/SearchController.php:61
* @route '/search/suggest'
*/
suggest.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: suggest.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\SearchController::suggest
* @see app/Http/Controllers/Site/SearchController.php:61
* @route '/search/suggest'
*/
suggest.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: suggest.url(options),
    method: 'head',
})

const search = {
    suggest: Object.assign(suggest, suggest),
}

export default search