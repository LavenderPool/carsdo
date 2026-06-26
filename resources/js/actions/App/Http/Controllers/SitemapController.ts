import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:18
* @route '/sitemap.xml'
*/
const SitemapController = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SitemapController.url(options),
    method: 'get',
})

SitemapController.definition = {
    methods: ["get","head"],
    url: '/sitemap.xml',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:18
* @route '/sitemap.xml'
*/
SitemapController.url = (options?: RouteQueryOptions) => {
    return SitemapController.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:18
* @route '/sitemap.xml'
*/
SitemapController.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: SitemapController.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\SitemapController::__invoke
* @see app/Http/Controllers/SitemapController.php:18
* @route '/sitemap.xml'
*/
SitemapController.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: SitemapController.url(options),
    method: 'head',
})

export default SitemapController