import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::index
* @see app/Http/Controllers/Admin/CarCrashTestController.php:16
* @route '/admin/cars/{car}/crash-tests'
*/
export const index = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/crash-tests',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::index
* @see app/Http/Controllers/Admin/CarCrashTestController.php:16
* @route '/admin/cars/{car}/crash-tests'
*/
index.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return index.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::index
* @see app/Http/Controllers/Admin/CarCrashTestController.php:16
* @route '/admin/cars/{car}/crash-tests'
*/
index.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::index
* @see app/Http/Controllers/Admin/CarCrashTestController.php:16
* @route '/admin/cars/{car}/crash-tests'
*/
index.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::create
* @see app/Http/Controllers/Admin/CarCrashTestController.php:46
* @route '/admin/cars/{car}/crash-tests/create'
*/
export const create = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/crash-tests/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::create
* @see app/Http/Controllers/Admin/CarCrashTestController.php:46
* @route '/admin/cars/{car}/crash-tests/create'
*/
create.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return create.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::create
* @see app/Http/Controllers/Admin/CarCrashTestController.php:46
* @route '/admin/cars/{car}/crash-tests/create'
*/
create.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::create
* @see app/Http/Controllers/Admin/CarCrashTestController.php:46
* @route '/admin/cars/{car}/crash-tests/create'
*/
create.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::store
* @see app/Http/Controllers/Admin/CarCrashTestController.php:78
* @route '/admin/cars/{car}/crash-tests'
*/
export const store = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/crash-tests',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::store
* @see app/Http/Controllers/Admin/CarCrashTestController.php:78
* @route '/admin/cars/{car}/crash-tests'
*/
store.url = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
    }

    return store.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::store
* @see app/Http/Controllers/Admin/CarCrashTestController.php:78
* @route '/admin/cars/{car}/crash-tests'
*/
store.post = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::edit
* @see app/Http/Controllers/Admin/CarCrashTestController.php:93
* @route '/admin/cars/{car}/crash-tests/{crashTest}/edit'
*/
export const edit = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/crash-tests/{crashTest}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::edit
* @see app/Http/Controllers/Admin/CarCrashTestController.php:93
* @route '/admin/cars/{car}/crash-tests/{crashTest}/edit'
*/
edit.url = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            crashTest: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        crashTest: typeof args.crashTest === 'object'
        ? args.crashTest.id
        : args.crashTest,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{crashTest}', parsedArgs.crashTest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::edit
* @see app/Http/Controllers/Admin/CarCrashTestController.php:93
* @route '/admin/cars/{car}/crash-tests/{crashTest}/edit'
*/
edit.get = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::edit
* @see app/Http/Controllers/Admin/CarCrashTestController.php:93
* @route '/admin/cars/{car}/crash-tests/{crashTest}/edit'
*/
edit.head = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::update
* @see app/Http/Controllers/Admin/CarCrashTestController.php:119
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
export const update = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/crash-tests/{crashTest}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::update
* @see app/Http/Controllers/Admin/CarCrashTestController.php:119
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
update.url = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            crashTest: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        crashTest: typeof args.crashTest === 'object'
        ? args.crashTest.id
        : args.crashTest,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{crashTest}', parsedArgs.crashTest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::update
* @see app/Http/Controllers/Admin/CarCrashTestController.php:119
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
update.put = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::update
* @see app/Http/Controllers/Admin/CarCrashTestController.php:119
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
update.patch = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::destroy
* @see app/Http/Controllers/Admin/CarCrashTestController.php:129
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
export const destroy = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/crash-tests/{crashTest}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::destroy
* @see app/Http/Controllers/Admin/CarCrashTestController.php:129
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
destroy.url = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            crashTest: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        crashTest: typeof args.crashTest === 'object'
        ? args.crashTest.id
        : args.crashTest,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{crashTest}', parsedArgs.crashTest.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarCrashTestController::destroy
* @see app/Http/Controllers/Admin/CarCrashTestController.php:129
* @route '/admin/cars/{car}/crash-tests/{crashTest}'
*/
destroy.delete = (args: { car: number | { id: number }, crashTest: number | { id: number } } | [car: number | { id: number }, crashTest: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarCrashTestController = { index, create, store, edit, update, destroy }

export default CarCrashTestController