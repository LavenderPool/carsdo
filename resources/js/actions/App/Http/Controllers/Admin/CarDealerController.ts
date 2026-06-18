import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarDealerController::index
* @see app/Http/Controllers/Admin/CarDealerController.php:19
* @route '/admin/car-dealers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/car-dealers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::index
* @see app/Http/Controllers/Admin/CarDealerController.php:19
* @route '/admin/car-dealers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::index
* @see app/Http/Controllers/Admin/CarDealerController.php:19
* @route '/admin/car-dealers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::index
* @see app/Http/Controllers/Admin/CarDealerController.php:19
* @route '/admin/car-dealers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::create
* @see app/Http/Controllers/Admin/CarDealerController.php:72
* @route '/admin/car-dealers/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/car-dealers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::create
* @see app/Http/Controllers/Admin/CarDealerController.php:72
* @route '/admin/car-dealers/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::create
* @see app/Http/Controllers/Admin/CarDealerController.php:72
* @route '/admin/car-dealers/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::create
* @see app/Http/Controllers/Admin/CarDealerController.php:72
* @route '/admin/car-dealers/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::store
* @see app/Http/Controllers/Admin/CarDealerController.php:79
* @route '/admin/car-dealers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/car-dealers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::store
* @see app/Http/Controllers/Admin/CarDealerController.php:79
* @route '/admin/car-dealers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::store
* @see app/Http/Controllers/Admin/CarDealerController.php:79
* @route '/admin/car-dealers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::edit
* @see app/Http/Controllers/Admin/CarDealerController.php:88
* @route '/admin/car-dealers/{car_dealer}/edit'
*/
export const edit = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/car-dealers/{car_dealer}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::edit
* @see app/Http/Controllers/Admin/CarDealerController.php:88
* @route '/admin/car-dealers/{car_dealer}/edit'
*/
edit.url = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_dealer: typeof args.car_dealer === 'object'
        ? args.car_dealer.id
        : args.car_dealer,
    }

    return edit.definition.url
            .replace('{car_dealer}', parsedArgs.car_dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::edit
* @see app/Http/Controllers/Admin/CarDealerController.php:88
* @route '/admin/car-dealers/{car_dealer}/edit'
*/
edit.get = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::edit
* @see app/Http/Controllers/Admin/CarDealerController.php:88
* @route '/admin/car-dealers/{car_dealer}/edit'
*/
edit.head = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::update
* @see app/Http/Controllers/Admin/CarDealerController.php:107
* @route '/admin/car-dealers/{car_dealer}'
*/
export const update = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/car-dealers/{car_dealer}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::update
* @see app/Http/Controllers/Admin/CarDealerController.php:107
* @route '/admin/car-dealers/{car_dealer}'
*/
update.url = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_dealer: typeof args.car_dealer === 'object'
        ? args.car_dealer.id
        : args.car_dealer,
    }

    return update.definition.url
            .replace('{car_dealer}', parsedArgs.car_dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::update
* @see app/Http/Controllers/Admin/CarDealerController.php:107
* @route '/admin/car-dealers/{car_dealer}'
*/
update.put = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::update
* @see app/Http/Controllers/Admin/CarDealerController.php:107
* @route '/admin/car-dealers/{car_dealer}'
*/
update.patch = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarDealerController::destroy
* @see app/Http/Controllers/Admin/CarDealerController.php:116
* @route '/admin/car-dealers/{car_dealer}'
*/
export const destroy = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/car-dealers/{car_dealer}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarDealerController::destroy
* @see app/Http/Controllers/Admin/CarDealerController.php:116
* @route '/admin/car-dealers/{car_dealer}'
*/
destroy.url = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { car_dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { car_dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            car_dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car_dealer: typeof args.car_dealer === 'object'
        ? args.car_dealer.id
        : args.car_dealer,
    }

    return destroy.definition.url
            .replace('{car_dealer}', parsedArgs.car_dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarDealerController::destroy
* @see app/Http/Controllers/Admin/CarDealerController.php:116
* @route '/admin/car-dealers/{car_dealer}'
*/
destroy.delete = (args: { car_dealer: string | number | { id: string | number } } | [car_dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarDealerController = { index, create, store, edit, update, destroy }

export default CarDealerController