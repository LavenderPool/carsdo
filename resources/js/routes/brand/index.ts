import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Site\BrandController::show
* @see app/Http/Controllers/Site/BrandController.php:51
* @route '/{brand}'
*/
export const show = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/{brand}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\BrandController::show
* @see app/Http/Controllers/Site/BrandController.php:51
* @route '/{brand}'
*/
show.url = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { brand: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { brand: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            brand: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
    }

    return show.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\BrandController::show
* @see app/Http/Controllers/Site/BrandController.php:51
* @route '/{brand}'
*/
show.get = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\BrandController::show
* @see app/Http/Controllers/Site/BrandController.php:51
* @route '/{brand}'
*/
show.head = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const brand = {
    show: Object.assign(show, show),
}

export default brand