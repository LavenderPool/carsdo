import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\BlogController::index
* @see app/Http/Controllers/Site/BlogController.php:14
* @route '/blog'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/blog',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\BlogController::index
* @see app/Http/Controllers/Site/BlogController.php:14
* @route '/blog'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\BlogController::index
* @see app/Http/Controllers/Site/BlogController.php:14
* @route '/blog'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\BlogController::index
* @see app/Http/Controllers/Site/BlogController.php:14
* @route '/blog'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\BlogController::show
* @see app/Http/Controllers/Site/BlogController.php:29
* @route '/blog/{article}'
*/
export const show = (args: { article: string | { slug: string } } | [article: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/blog/{article}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\BlogController::show
* @see app/Http/Controllers/Site/BlogController.php:29
* @route '/blog/{article}'
*/
show.url = (args: { article: string | { slug: string } } | [article: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { article: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'slug' in args) {
        args = { article: args.slug }
    }

    if (Array.isArray(args)) {
        args = {
            article: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        article: typeof args.article === 'object'
        ? args.article.slug
        : args.article,
    }

    return show.definition.url
            .replace('{article}', parsedArgs.article.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\BlogController::show
* @see app/Http/Controllers/Site/BlogController.php:29
* @route '/blog/{article}'
*/
show.get = (args: { article: string | { slug: string } } | [article: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\BlogController::show
* @see app/Http/Controllers/Site/BlogController.php:29
* @route '/blog/{article}'
*/
show.head = (args: { article: string | { slug: string } } | [article: string | { slug: string } ] | string | { slug: string }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const BlogController = { index, show }

export default BlogController