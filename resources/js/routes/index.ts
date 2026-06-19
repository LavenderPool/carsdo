import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../wayfinder'
/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:22
* @route '/register'
*/
export const register = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

register.definition = {
    methods: ["get","head"],
    url: '/register',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:22
* @route '/register'
*/
register.url = (options?: RouteQueryOptions) => {
    return register.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:22
* @route '/register'
*/
register.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: register.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\RegisteredUserController::register
* @see app/Http/Controllers/Auth/RegisteredUserController.php:22
* @route '/register'
*/
register.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: register.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
export const login = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

login.definition = {
    methods: ["get","head"],
    url: '/login',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.url = (options?: RouteQueryOptions) => {
    return login.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: login.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::login
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:18
* @route '/login'
*/
login.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: login.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:40
* @route '/logout'
*/
export const logout = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

logout.definition = {
    methods: ["post"],
    url: '/logout',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:40
* @route '/logout'
*/
logout.url = (options?: RouteQueryOptions) => {
    return logout.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\AuthenticatedSessionController::logout
* @see app/Http/Controllers/Auth/AuthenticatedSessionController.php:40
* @route '/logout'
*/
logout.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: logout.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Site\HomeController::__invoke
* @see app/Http/Controllers/Site/HomeController.php:14
* @route '/'
*/
export const home = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

home.definition = {
    methods: ["get","head"],
    url: '/',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\HomeController::__invoke
* @see app/Http/Controllers/Site/HomeController.php:14
* @route '/'
*/
home.url = (options?: RouteQueryOptions) => {
    return home.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\HomeController::__invoke
* @see app/Http/Controllers/Site/HomeController.php:14
* @route '/'
*/
home.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: home.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\HomeController::__invoke
* @see app/Http/Controllers/Site/HomeController.php:14
* @route '/'
*/
home.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: home.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
export const newCars = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newCars.url(args, options),
    method: 'get',
})

newCars.definition = {
    methods: ["get","head"],
    url: '/new-cars-{year}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
newCars.url = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { year: args }
    }

    if (Array.isArray(args)) {
        args = {
            year: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        year: args.year,
    }

    return newCars.definition.url
            .replace('{year}', parsedArgs.year.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
newCars.get = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: newCars.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
newCars.head = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: newCars.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
export const electricCars = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electricCars.url(options),
    method: 'get',
})

electricCars.definition = {
    methods: ["get","head"],
    url: '/electric-cars',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
electricCars.url = (options?: RouteQueryOptions) => {
    return electricCars.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
electricCars.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: electricCars.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\ElectricCarController::__invoke
* @see app/Http/Controllers/Site/ElectricCarController.php:14
* @route '/electric-cars'
*/
electricCars.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: electricCars.url(options),
    method: 'head',
})

