import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::index
* @see app/Http/Controllers/Admin/CarConfigurationController.php:16
* @route '/admin/cars/{car}/configurations'
*/
export const index = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configurations',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::index
* @see app/Http/Controllers/Admin/CarConfigurationController.php:16
* @route '/admin/cars/{car}/configurations'
*/
index.url = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Admin\CarConfigurationController::index
* @see app/Http/Controllers/Admin/CarConfigurationController.php:16
* @route '/admin/cars/{car}/configurations'
*/
index.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::index
* @see app/Http/Controllers/Admin/CarConfigurationController.php:16
* @route '/admin/cars/{car}/configurations'
*/
index.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::create
* @see app/Http/Controllers/Admin/CarConfigurationController.php:52
* @route '/admin/cars/{car}/configurations/create'
*/
export const create = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configurations/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::create
* @see app/Http/Controllers/Admin/CarConfigurationController.php:52
* @route '/admin/cars/{car}/configurations/create'
*/
create.url = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Admin\CarConfigurationController::create
* @see app/Http/Controllers/Admin/CarConfigurationController.php:52
* @route '/admin/cars/{car}/configurations/create'
*/
create.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::create
* @see app/Http/Controllers/Admin/CarConfigurationController.php:52
* @route '/admin/cars/{car}/configurations/create'
*/
create.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::store
* @see app/Http/Controllers/Admin/CarConfigurationController.php:96
* @route '/admin/cars/{car}/configurations'
*/
export const store = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/configurations',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::store
* @see app/Http/Controllers/Admin/CarConfigurationController.php:96
* @route '/admin/cars/{car}/configurations'
*/
store.url = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
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
* @see \App\Http\Controllers\Admin\CarConfigurationController::store
* @see app/Http/Controllers/Admin/CarConfigurationController.php:96
* @route '/admin/cars/{car}/configurations'
*/
store.post = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::edit
* @see app/Http/Controllers/Admin/CarConfigurationController.php:108
* @route '/admin/cars/{car}/configurations/{configuration}/edit'
*/
export const edit = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configurations/{configuration}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::edit
* @see app/Http/Controllers/Admin/CarConfigurationController.php:108
* @route '/admin/cars/{car}/configurations/{configuration}/edit'
*/
edit.url = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configuration: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configuration: typeof args.configuration === 'object'
        ? args.configuration.id
        : args.configuration,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configuration}', parsedArgs.configuration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::edit
* @see app/Http/Controllers/Admin/CarConfigurationController.php:108
* @route '/admin/cars/{car}/configurations/{configuration}/edit'
*/
edit.get = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::edit
* @see app/Http/Controllers/Admin/CarConfigurationController.php:108
* @route '/admin/cars/{car}/configurations/{configuration}/edit'
*/
edit.head = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::update
* @see app/Http/Controllers/Admin/CarConfigurationController.php:154
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
export const update = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/configurations/{configuration}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::update
* @see app/Http/Controllers/Admin/CarConfigurationController.php:154
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
update.url = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configuration: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configuration: typeof args.configuration === 'object'
        ? args.configuration.id
        : args.configuration,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configuration}', parsedArgs.configuration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::update
* @see app/Http/Controllers/Admin/CarConfigurationController.php:154
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
update.put = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::update
* @see app/Http/Controllers/Admin/CarConfigurationController.php:154
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
update.patch = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationController.php:169
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
export const destroy = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/configurations/{configuration}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationController.php:169
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
destroy.url = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configuration: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configuration: typeof args.configuration === 'object'
        ? args.configuration.id
        : args.configuration,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configuration}', parsedArgs.configuration.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationController.php:169
* @route '/admin/cars/{car}/configurations/{configuration}'
*/
destroy.delete = (args: { car: string | number | { id: string | number }, configuration: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configuration: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const configurations = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default configurations