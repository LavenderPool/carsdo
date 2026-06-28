import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Site\EngineController::index
* @see app/Http/Controllers/Site/EngineController.php:14
* @route '/engine'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/engine',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\EngineController::index
* @see app/Http/Controllers/Site/EngineController.php:14
* @route '/engine'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\EngineController::index
* @see app/Http/Controllers/Site/EngineController.php:14
* @route '/engine'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\EngineController::index
* @see app/Http/Controllers/Site/EngineController.php:14
* @route '/engine'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\EngineController::brand
* @see app/Http/Controllers/Site/EngineController.php:38
* @route '/engine/{brand}'
*/
export const brand = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

brand.definition = {
    methods: ["get","head"],
    url: '/engine/{brand}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\EngineController::brand
* @see app/Http/Controllers/Site/EngineController.php:38
* @route '/engine/{brand}'
*/
brand.url = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
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

    return brand.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\EngineController::brand
* @see app/Http/Controllers/Site/EngineController.php:38
* @route '/engine/{brand}'
*/
brand.get = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\EngineController::brand
* @see app/Http/Controllers/Site/EngineController.php:38
* @route '/engine/{brand}'
*/
brand.head = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: brand.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\EngineController::show
* @see app/Http/Controllers/Site/EngineController.php:64
* @route '/engine/{brand}/{engine_slug}'
*/
export const show = (args: { brand: string | { slug: string }, engine_slug: string | number } | [brand: string | { slug: string }, engine_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/engine/{brand}/{engine_slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\EngineController::show
* @see app/Http/Controllers/Site/EngineController.php:64
* @route '/engine/{brand}/{engine_slug}'
*/
show.url = (args: { brand: string | { slug: string }, engine_slug: string | number } | [brand: string | { slug: string }, engine_slug: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            engine_slug: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        engine_slug: args.engine_slug,
    }

    return show.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{engine_slug}', parsedArgs.engine_slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\EngineController::show
* @see app/Http/Controllers/Site/EngineController.php:64
* @route '/engine/{brand}/{engine_slug}'
*/
show.get = (args: { brand: string | { slug: string }, engine_slug: string | number } | [brand: string | { slug: string }, engine_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\EngineController::show
* @see app/Http/Controllers/Site/EngineController.php:64
* @route '/engine/{brand}/{engine_slug}'
*/
show.head = (args: { brand: string | { slug: string }, engine_slug: string | number } | [brand: string | { slug: string }, engine_slug: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const engine = {
    index: Object.assign(index, index),
    brand: Object.assign(brand, brand),
    show: Object.assign(show, show),
}

export default engine