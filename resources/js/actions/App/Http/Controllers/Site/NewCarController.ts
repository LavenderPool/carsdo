import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
const NewCarController = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: NewCarController.url(args, options),
    method: 'get',
})

NewCarController.definition = {
    methods: ["get","head"],
    url: '/new-cars-{year}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
NewCarController.url = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions) => {
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

    return NewCarController.definition.url
            .replace('{year}', parsedArgs.year.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
NewCarController.get = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: NewCarController.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\NewCarController::__invoke
* @see app/Http/Controllers/Site/NewCarController.php:13
* @route '/new-cars-{year}'
*/
NewCarController.head = (args: { year: string | number } | [year: string | number ] | string | number, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: NewCarController.url(args, options),
    method: 'head',
})

export default NewCarController