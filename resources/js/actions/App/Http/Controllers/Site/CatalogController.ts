import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\CatalogController::__invoke
* @see app/Http/Controllers/Site/CatalogController.php:14
* @route '/catalogs/{catalog}'
*/
const CatalogController = (args: { catalog: string | { slug: string } } | [catalog: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CatalogController.url(args, options),
    method: 'get',
})

CatalogController.definition = {
    methods: ["get","head"],
    url: '/catalogs/{catalog}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CatalogController::__invoke
* @see app/Http/Controllers/Site/CatalogController.php:14
* @route '/catalogs/{catalog}'
*/
CatalogController.url = (args: { catalog: string | { slug: string } } | [catalog: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { catalog: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { catalog: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            catalog: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        catalog: typeof args.catalog === 'object'
        ? args.catalog.slug
        : args.catalog,
    }

    return CatalogController.definition.url
            .replace('{catalog}', parsedArgs.catalog.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CatalogController::__invoke
* @see app/Http/Controllers/Site/CatalogController.php:14
* @route '/catalogs/{catalog}'
*/
CatalogController.get = (args: { catalog: string | { slug: string } } | [catalog: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CatalogController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CatalogController::__invoke
* @see app/Http/Controllers/Site/CatalogController.php:14
* @route '/catalogs/{catalog}'
*/
CatalogController.head = (args: { catalog: string | { slug: string } } | [catalog: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CatalogController.url(args, options),
    method: 'head',
})

export default CatalogController