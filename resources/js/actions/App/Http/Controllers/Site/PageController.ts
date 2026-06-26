import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:18
* @route '/privacy-policy'
*/
export const privacy = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

privacy.definition = {
    methods: ["get","head"],
    url: '/privacy-policy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:18
* @route '/privacy-policy'
*/
privacy.url = (options?: RouteQueryOptions) => {
    return privacy.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:18
* @route '/privacy-policy'
*/
privacy.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: privacy.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::privacy
* @see app/Http/Controllers/Site/PageController.php:18
* @route '/privacy-policy'
*/
privacy.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: privacy.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::cookie
* @see app/Http/Controllers/Site/PageController.php:23
* @route '/cookie-policy'
*/
export const cookie = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cookie.url(options),
    method: 'get',
})

cookie.definition = {
    methods: ["get","head"],
    url: '/cookie-policy',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::cookie
* @see app/Http/Controllers/Site/PageController.php:23
* @route '/cookie-policy'
*/
cookie.url = (options?: RouteQueryOptions) => {
    return cookie.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::cookie
* @see app/Http/Controllers/Site/PageController.php:23
* @route '/cookie-policy'
*/
cookie.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: cookie.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::cookie
* @see app/Http/Controllers/Site/PageController.php:23
* @route '/cookie-policy'
*/
cookie.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: cookie.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::contacts
* @see app/Http/Controllers/Site/PageController.php:28
* @route '/contacts'
*/
export const contacts = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contacts.url(options),
    method: 'get',
})

contacts.definition = {
    methods: ["get","head"],
    url: '/contacts',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::contacts
* @see app/Http/Controllers/Site/PageController.php:28
* @route '/contacts'
*/
contacts.url = (options?: RouteQueryOptions) => {
    return contacts.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::contacts
* @see app/Http/Controllers/Site/PageController.php:28
* @route '/contacts'
*/
contacts.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: contacts.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::contacts
* @see app/Http/Controllers/Site/PageController.php:28
* @route '/contacts'
*/
contacts.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: contacts.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\PageController::show
* @see app/Http/Controllers/Site/PageController.php:13
* @route '/pages/{slug}'
*/
export const show = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/pages/{slug}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\PageController::show
* @see app/Http/Controllers/Site/PageController.php:13
* @route '/pages/{slug}'
*/
show.url = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { slug: args }
    }

    if (Array.isArray(args)) {
        args = {
            slug: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        slug: args.slug,
    }

    return show.definition.url
            .replace('{slug}', parsedArgs.slug.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\PageController::show
* @see app/Http/Controllers/Site/PageController.php:13
* @route '/pages/{slug}'
*/
show.get = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\PageController::show
* @see app/Http/Controllers/Site/PageController.php:13
* @route '/pages/{slug}'
*/
show.head = (args: { slug: string | number } | [slug: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const PageController = { privacy, cookie, contacts, show }

export default PageController