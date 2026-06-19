import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\TestDriveController::index
* @see app/Http/Controllers/Site/TestDriveController.php:13
* @route '/test-drive'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/test-drive',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\TestDriveController::index
* @see app/Http/Controllers/Site/TestDriveController.php:13
* @route '/test-drive'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\TestDriveController::index
* @see app/Http/Controllers/Site/TestDriveController.php:13
* @route '/test-drive'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\TestDriveController::index
* @see app/Http/Controllers/Site/TestDriveController.php:13
* @route '/test-drive'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\TestDriveController::electric
* @see app/Http/Controllers/Site/TestDriveController.php:37
* @route '/test-drive/electric-cars'
*/
export const electric = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electric.url(options),
    method: 'get',
})

electric.definition = {
    methods: ["get","head"],
    url: '/test-drive/electric-cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\TestDriveController::electric
* @see app/Http/Controllers/Site/TestDriveController.php:37
* @route '/test-drive/electric-cars'
*/
electric.url = (options?: RouteQueryOptions) => {
    return electric.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\TestDriveController::electric
* @see app/Http/Controllers/Site/TestDriveController.php:37
* @route '/test-drive/electric-cars'
*/
electric.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electric.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\TestDriveController::electric
* @see app/Http/Controllers/Site/TestDriveController.php:37
* @route '/test-drive/electric-cars'
*/
electric.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: electric.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\TestDriveController::brand
* @see app/Http/Controllers/Site/TestDriveController.php:63
* @route '/test-drive/{brand}'
*/
export const brand = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

brand.definition = {
    methods: ["get","head"],
    url: '/test-drive/{brand}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\TestDriveController::brand
* @see app/Http/Controllers/Site/TestDriveController.php:63
* @route '/test-drive/{brand}'
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
* @see \App\Http\Controllers\Site\TestDriveController::brand
* @see app/Http/Controllers/Site/TestDriveController.php:63
* @route '/test-drive/{brand}'
*/
brand.get = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\TestDriveController::brand
* @see app/Http/Controllers/Site/TestDriveController.php:63
* @route '/test-drive/{brand}'
*/
brand.head = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: brand.url(args, options),
    method: 'head',
})

const TestDriveController = { index, electric, brand }

export default TestDriveController