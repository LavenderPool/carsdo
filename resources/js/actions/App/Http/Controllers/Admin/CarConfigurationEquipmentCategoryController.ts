import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:16
* @route '/admin/cars/{car}/equipment-categories'
*/
export const index = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment-categories',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:16
* @route '/admin/cars/{car}/equipment-categories'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:16
* @route '/admin/cars/{car}/equipment-categories'
*/
index.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::index
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:16
* @route '/admin/cars/{car}/equipment-categories'
*/
index.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:51
* @route '/admin/cars/{car}/equipment-categories/create'
*/
export const create = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment-categories/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:51
* @route '/admin/cars/{car}/equipment-categories/create'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:51
* @route '/admin/cars/{car}/equipment-categories/create'
*/
create.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::create
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:51
* @route '/admin/cars/{car}/equipment-categories/create'
*/
create.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:77
* @route '/admin/cars/{car}/equipment-categories'
*/
export const store = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/equipment-categories',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:77
* @route '/admin/cars/{car}/equipment-categories'
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
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::store
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:77
* @route '/admin/cars/{car}/equipment-categories'
*/
store.post = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:93
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}/edit'
*/
export const edit = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/equipment-categories/{equipmentCategory}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:93
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}/edit'
*/
edit.url = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipmentCategory: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipmentCategory: typeof args.equipmentCategory === 'object'
        ? args.equipmentCategory.id
        : args.equipmentCategory,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipmentCategory}', parsedArgs.equipmentCategory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:93
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}/edit'
*/
edit.get = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::edit
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:93
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}/edit'
*/
edit.head = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:121
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
export const update = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/equipment-categories/{equipmentCategory}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:121
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
update.url = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipmentCategory: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipmentCategory: typeof args.equipmentCategory === 'object'
        ? args.equipmentCategory.id
        : args.equipmentCategory,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipmentCategory}', parsedArgs.equipmentCategory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:121
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
update.put = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::update
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:121
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
update.patch = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:142
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
export const destroy = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/equipment-categories/{equipmentCategory}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:142
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
destroy.url = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            equipmentCategory: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        equipmentCategory: typeof args.equipmentCategory === 'object'
        ? args.equipmentCategory.id
        : args.equipmentCategory,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{equipmentCategory}', parsedArgs.equipmentCategory.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarConfigurationEquipmentCategoryController::destroy
* @see app/Http/Controllers/Admin/CarConfigurationEquipmentCategoryController.php:142
* @route '/admin/cars/{car}/equipment-categories/{equipmentCategory}'
*/
destroy.delete = (args: { car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } } | [car: string | number | { id: string | number }, equipmentCategory: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarConfigurationEquipmentCategoryController = { index, create, store, edit, update, destroy }

export default CarConfigurationEquipmentCategoryController