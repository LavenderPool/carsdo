import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::index
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:16
* @route '/admin/cars/{car}/configuration-groups'
*/
export const index = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configuration-groups',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::index
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:16
* @route '/admin/cars/{car}/configuration-groups'
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
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::index
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:16
* @route '/admin/cars/{car}/configuration-groups'
*/
index.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::index
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:16
* @route '/admin/cars/{car}/configuration-groups'
*/
index.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::create
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:48
* @route '/admin/cars/{car}/configuration-groups/create'
*/
export const create = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configuration-groups/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::create
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:48
* @route '/admin/cars/{car}/configuration-groups/create'
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
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::create
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:48
* @route '/admin/cars/{car}/configuration-groups/create'
*/
create.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::create
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:48
* @route '/admin/cars/{car}/configuration-groups/create'
*/
create.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::store
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:72
* @route '/admin/cars/{car}/configuration-groups'
*/
export const store = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/configuration-groups',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::store
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:72
* @route '/admin/cars/{car}/configuration-groups'
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
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::store
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:72
* @route '/admin/cars/{car}/configuration-groups'
*/
store.post = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::edit
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:81
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}/edit'
*/
export const edit = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/configuration-groups/{configurationGroup}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::edit
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:81
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}/edit'
*/
edit.url = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configurationGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configurationGroup: typeof args.configurationGroup === 'object'
        ? args.configurationGroup.id
        : args.configurationGroup,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configurationGroup}', parsedArgs.configurationGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::edit
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:81
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}/edit'
*/
edit.get = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::edit
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:81
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}/edit'
*/
edit.head = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::update
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:107
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
export const update = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/configuration-groups/{configurationGroup}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::update
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:107
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
update.url = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configurationGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configurationGroup: typeof args.configurationGroup === 'object'
        ? args.configurationGroup.id
        : args.configurationGroup,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configurationGroup}', parsedArgs.configurationGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::update
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:107
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
update.put = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::update
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:107
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
update.patch = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:120
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
export const destroy = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/configuration-groups/{configurationGroup}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:120
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
destroy.url = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            configurationGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        configurationGroup: typeof args.configurationGroup === 'object'
        ? args.configurationGroup.id
        : args.configurationGroup,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{configurationGroup}', parsedArgs.configurationGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationGroupController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationGroupController.php:120
* @route '/admin/cars/{car}/configuration-groups/{configurationGroup}'
*/
destroy.delete = (args: { car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, configurationGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const configurationGroups = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default configurationGroups