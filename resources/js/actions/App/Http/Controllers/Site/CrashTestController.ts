import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\CrashTestController::index
* @see app/Http/Controllers/Site/CrashTestController.php:13
* @route '/crash-test'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/crash-test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CrashTestController::index
* @see app/Http/Controllers/Site/CrashTestController.php:13
* @route '/crash-test'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CrashTestController::index
* @see app/Http/Controllers/Site/CrashTestController.php:13
* @route '/crash-test'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CrashTestController::index
* @see app/Http/Controllers/Site/CrashTestController.php:13
* @route '/crash-test'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CrashTestController::electric
* @see app/Http/Controllers/Site/CrashTestController.php:30
* @route '/crash-test/electric-cars'
*/
export const electric = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electric.url(options),
    method: 'get',
})

electric.definition = {
    methods: ["get","head"],
    url: '/crash-test/electric-cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CrashTestController::electric
* @see app/Http/Controllers/Site/CrashTestController.php:30
* @route '/crash-test/electric-cars'
*/
electric.url = (options?: RouteQueryOptions) => {
    return electric.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CrashTestController::electric
* @see app/Http/Controllers/Site/CrashTestController.php:30
* @route '/crash-test/electric-cars'
*/
electric.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electric.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CrashTestController::electric
* @see app/Http/Controllers/Site/CrashTestController.php:30
* @route '/crash-test/electric-cars'
*/
electric.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: electric.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CrashTestController::brand
* @see app/Http/Controllers/Site/CrashTestController.php:48
* @route '/crash-test/{brand}'
*/
export const brand = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

brand.definition = {
    methods: ["get","head"],
    url: '/crash-test/{brand}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CrashTestController::brand
* @see app/Http/Controllers/Site/CrashTestController.php:48
* @route '/crash-test/{brand}'
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
* @see \App\Http\Controllers\Site\CrashTestController::brand
* @see app/Http/Controllers/Site/CrashTestController.php:48
* @route '/crash-test/{brand}'
*/
brand.get = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CrashTestController::brand
* @see app/Http/Controllers/Site/CrashTestController.php:48
* @route '/crash-test/{brand}'
*/
brand.head = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: brand.url(args, options),
    method: 'head',
})

const CrashTestController = { index, electric, brand }

export default CrashTestController