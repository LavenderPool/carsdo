import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::index
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:13
* @route '/cars-photo'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/cars-photo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::index
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:13
* @route '/cars-photo'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::index
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:13
* @route '/cars-photo'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::index
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:13
* @route '/cars-photo'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::brand
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:32
* @route '/cars-photo/{brand}'
*/
export const brand = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

brand.definition = {
    methods: ["get","head"],
    url: '/cars-photo/{brand}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::brand
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:32
* @route '/cars-photo/{brand}'
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
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::brand
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:32
* @route '/cars-photo/{brand}'
*/
brand.get = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: brand.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarPhotoGalleryController::brand
* @see app/Http/Controllers/Site/CarPhotoGalleryController.php:32
* @route '/cars-photo/{brand}'
*/
brand.head = (args: { brand: string | { slug: string } } | [brand: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: brand.url(args, options),
    method: 'head',
})

const CarPhotoGalleryController = { index, brand }

export default CarPhotoGalleryController