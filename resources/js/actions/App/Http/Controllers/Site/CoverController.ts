import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\CoverController::__invoke
* @see app/Http/Controllers/Site/CoverController.php:10
* @route '/covers/{brand_slug}/{car_slug}/cover.jpg'
*/
const CoverController = (args: { brand_slug: string | number, car_slug: string | number } | [brand_slug: string | number, car_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CoverController.url(args, options),
    method: 'get',
})

CoverController.definition = {
    methods: ["get","head"],
    url: '/covers/{brand_slug}/{car_slug}/cover.jpg',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CoverController::__invoke
* @see app/Http/Controllers/Site/CoverController.php:10
* @route '/covers/{brand_slug}/{car_slug}/cover.jpg'
*/
CoverController.url = (args: { brand_slug: string | number, car_slug: string | number } | [brand_slug: string | number, car_slug: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand_slug: args[0],
            car_slug: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand_slug: args.brand_slug,
        car_slug: args.car_slug,
    }

    return CoverController.definition.url
            .replace('{brand_slug}', parsedArgs.brand_slug.toString())
            .replace('{car_slug}', parsedArgs.car_slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CoverController::__invoke
* @see app/Http/Controllers/Site/CoverController.php:10
* @route '/covers/{brand_slug}/{car_slug}/cover.jpg'
*/
CoverController.get = (args: { brand_slug: string | number, car_slug: string | number } | [brand_slug: string | number, car_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: CoverController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CoverController::__invoke
* @see app/Http/Controllers/Site/CoverController.php:10
* @route '/covers/{brand_slug}/{car_slug}/cover.jpg'
*/
CoverController.head = (args: { brand_slug: string | number, car_slug: string | number } | [brand_slug: string | number, car_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: CoverController.url(args, options),
    method: 'head',
})

export default CoverController