import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::index
* @see app/Http/Controllers/Admin/CarTestDriveController.php:16
* @route '/admin/cars/{car}/test-drives'
*/
export const index = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/test-drives',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::index
* @see app/Http/Controllers/Admin/CarTestDriveController.php:16
* @route '/admin/cars/{car}/test-drives'
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
* @see \App\Http\Controllers\Admin\CarTestDriveController::index
* @see app/Http/Controllers/Admin/CarTestDriveController.php:16
* @route '/admin/cars/{car}/test-drives'
*/
index.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::index
* @see app/Http/Controllers/Admin/CarTestDriveController.php:16
* @route '/admin/cars/{car}/test-drives'
*/
index.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::create
* @see app/Http/Controllers/Admin/CarTestDriveController.php:47
* @route '/admin/cars/{car}/test-drives/create'
*/
export const create = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/test-drives/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::create
* @see app/Http/Controllers/Admin/CarTestDriveController.php:47
* @route '/admin/cars/{car}/test-drives/create'
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
* @see \App\Http\Controllers\Admin\CarTestDriveController::create
* @see app/Http/Controllers/Admin/CarTestDriveController.php:47
* @route '/admin/cars/{car}/test-drives/create'
*/
create.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::create
* @see app/Http/Controllers/Admin/CarTestDriveController.php:47
* @route '/admin/cars/{car}/test-drives/create'
*/
create.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::store
* @see app/Http/Controllers/Admin/CarTestDriveController.php:71
* @route '/admin/cars/{car}/test-drives'
*/
export const store = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/test-drives',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::store
* @see app/Http/Controllers/Admin/CarTestDriveController.php:71
* @route '/admin/cars/{car}/test-drives'
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
* @see \App\Http\Controllers\Admin\CarTestDriveController::store
* @see app/Http/Controllers/Admin/CarTestDriveController.php:71
* @route '/admin/cars/{car}/test-drives'
*/
store.post = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::edit
* @see app/Http/Controllers/Admin/CarTestDriveController.php:80
* @route '/admin/cars/{car}/test-drives/{testDrive}/edit'
*/
export const edit = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/test-drives/{testDrive}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::edit
* @see app/Http/Controllers/Admin/CarTestDriveController.php:80
* @route '/admin/cars/{car}/test-drives/{testDrive}/edit'
*/
edit.url = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            testDrive: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        testDrive: typeof args.testDrive === 'object'
        ? args.testDrive.id
        : args.testDrive,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{testDrive}', parsedArgs.testDrive.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::edit
* @see app/Http/Controllers/Admin/CarTestDriveController.php:80
* @route '/admin/cars/{car}/test-drives/{testDrive}/edit'
*/
edit.get = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::edit
* @see app/Http/Controllers/Admin/CarTestDriveController.php:80
* @route '/admin/cars/{car}/test-drives/{testDrive}/edit'
*/
edit.head = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::update
* @see app/Http/Controllers/Admin/CarTestDriveController.php:106
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
export const update = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/test-drives/{testDrive}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::update
* @see app/Http/Controllers/Admin/CarTestDriveController.php:106
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
update.url = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            testDrive: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        testDrive: typeof args.testDrive === 'object'
        ? args.testDrive.id
        : args.testDrive,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{testDrive}', parsedArgs.testDrive.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::update
* @see app/Http/Controllers/Admin/CarTestDriveController.php:106
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
update.put = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::update
* @see app/Http/Controllers/Admin/CarTestDriveController.php:106
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
update.patch = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::destroy
* @see app/Http/Controllers/Admin/CarTestDriveController.php:116
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
export const destroy = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/test-drives/{testDrive}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::destroy
* @see app/Http/Controllers/Admin/CarTestDriveController.php:116
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
destroy.url = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            testDrive: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        testDrive: typeof args.testDrive === 'object'
        ? args.testDrive.id
        : args.testDrive,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{testDrive}', parsedArgs.testDrive.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarTestDriveController::destroy
* @see app/Http/Controllers/Admin/CarTestDriveController.php:116
* @route '/admin/cars/{car}/test-drives/{testDrive}'
*/
destroy.delete = (args: { car: number | { id: number }, testDrive: number | { id: number } } | [car: number | { id: number }, testDrive: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const testDrives = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default testDrives