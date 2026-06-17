import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:17
* @route '/admin/cars/{car}/equipment'
*/
export const index = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:17
* @route '/admin/cars/{car}/equipment'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:17
* @route '/admin/cars/{car}/equipment'
*/
index.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:17
* @route '/admin/cars/{car}/equipment'
*/
index.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:54
* @route '/admin/cars/{car}/equipment/create'
*/
export const create = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:54
* @route '/admin/cars/{car}/equipment/create'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:54
* @route '/admin/cars/{car}/equipment/create'
*/
create.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:54
* @route '/admin/cars/{car}/equipment/create'
*/
create.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:82
* @route '/admin/cars/{car}/equipment'
*/
export const store = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/equipment',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:82
* @route '/admin/cars/{car}/equipment'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:82
* @route '/admin/cars/{car}/equipment'
*/
store.post = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:95
* @route '/admin/cars/{car}/equipment/{equipment}/edit'
*/
export const edit = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment/{equipment}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:95
* @route '/admin/cars/{car}/equipment/{equipment}/edit'
*/
edit.url = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipment: typeof args.equipment === 'object'
        ? args.equipment.id
        : args.equipment,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:95
* @route '/admin/cars/{car}/equipment/{equipment}/edit'
*/
edit.get = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:95
* @route '/admin/cars/{car}/equipment/{equipment}/edit'
*/
edit.head = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:125
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
export const update = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/equipment/{equipment}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:125
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
update.url = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipment: typeof args.equipment === 'object'
        ? args.equipment.id
        : args.equipment,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:125
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
update.put = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:125
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
update.patch = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:143
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
export const destroy = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/equipment/{equipment}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:143
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
destroy.url = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipment: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipment: typeof args.equipment === 'object'
        ? args.equipment.id
        : args.equipment,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipment}', parsedArgs.equipment.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentController.php:143
* @route '/admin/cars/{car}/equipment/{equipment}'
*/
destroy.delete = (args: { car: number | { id: number }, equipment: number | { id: number } } | [car: number | { id: number }, equipment: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarConfigurationEquipmentController = { index, create, store, edit, update, destroy }

export default CarConfigurationEquipmentController