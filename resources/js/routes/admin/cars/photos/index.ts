import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarPhotoController::index
* @see app/Http/Controllers/Admin/CarPhotoController.php:17
* @route '/admin/cars/{car}/photos'
*/
export const index = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photos',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::index
* @see app/Http/Controllers/Admin/CarPhotoController.php:17
* @route '/admin/cars/{car}/photos'
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
* @see \App\Http\Controllers\Admin\CarPhotoController::index
* @see app/Http/Controllers/Admin/CarPhotoController.php:17
* @route '/admin/cars/{car}/photos'
*/
index.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::index
* @see app/Http/Controllers/Admin/CarPhotoController.php:17
* @route '/admin/cars/{car}/photos'
*/
index.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::create
* @see app/Http/Controllers/Admin/CarPhotoController.php:51
* @route '/admin/cars/{car}/photos/create'
*/
export const create = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photos/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::create
* @see app/Http/Controllers/Admin/CarPhotoController.php:51
* @route '/admin/cars/{car}/photos/create'
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
* @see \App\Http\Controllers\Admin\CarPhotoController::create
* @see app/Http/Controllers/Admin/CarPhotoController.php:51
* @route '/admin/cars/{car}/photos/create'
*/
create.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::create
* @see app/Http/Controllers/Admin/CarPhotoController.php:51
* @route '/admin/cars/{car}/photos/create'
*/
create.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::store
* @see app/Http/Controllers/Admin/CarPhotoController.php:73
* @route '/admin/cars/{car}/photos'
*/
export const store = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/photos',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::store
* @see app/Http/Controllers/Admin/CarPhotoController.php:73
* @route '/admin/cars/{car}/photos'
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
* @see \App\Http\Controllers\Admin\CarPhotoController::store
* @see app/Http/Controllers/Admin/CarPhotoController.php:73
* @route '/admin/cars/{car}/photos'
*/
store.post = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::edit
* @see app/Http/Controllers/Admin/CarPhotoController.php:88
* @route '/admin/cars/{car}/photos/{photo}/edit'
*/
export const edit = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photos/{photo}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::edit
* @see app/Http/Controllers/Admin/CarPhotoController.php:88
* @route '/admin/cars/{car}/photos/{photo}/edit'
*/
edit.url = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photo: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photo: typeof args.photo === 'object'
        ? args.photo.id
        : args.photo,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photo}', parsedArgs.photo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::edit
* @see app/Http/Controllers/Admin/CarPhotoController.php:88
* @route '/admin/cars/{car}/photos/{photo}/edit'
*/
edit.get = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::edit
* @see app/Http/Controllers/Admin/CarPhotoController.php:88
* @route '/admin/cars/{car}/photos/{photo}/edit'
*/
edit.head = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::update
* @see app/Http/Controllers/Admin/CarPhotoController.php:114
* @route '/admin/cars/{car}/photos/{photo}'
*/
export const update = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/photos/{photo}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::update
* @see app/Http/Controllers/Admin/CarPhotoController.php:114
* @route '/admin/cars/{car}/photos/{photo}'
*/
update.url = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photo: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photo: typeof args.photo === 'object'
        ? args.photo.id
        : args.photo,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photo}', parsedArgs.photo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::update
* @see app/Http/Controllers/Admin/CarPhotoController.php:114
* @route '/admin/cars/{car}/photos/{photo}'
*/
update.put = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::update
* @see app/Http/Controllers/Admin/CarPhotoController.php:114
* @route '/admin/cars/{car}/photos/{photo}'
*/
update.patch = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::destroy
* @see app/Http/Controllers/Admin/CarPhotoController.php:136
* @route '/admin/cars/{car}/photos/{photo}'
*/
export const destroy = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/photos/{photo}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::destroy
* @see app/Http/Controllers/Admin/CarPhotoController.php:136
* @route '/admin/cars/{car}/photos/{photo}'
*/
destroy.url = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photo: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photo: typeof args.photo === 'object'
        ? args.photo.id
        : args.photo,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photo}', parsedArgs.photo.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoController::destroy
* @see app/Http/Controllers/Admin/CarPhotoController.php:136
* @route '/admin/cars/{car}/photos/{photo}'
*/
destroy.delete = (args: { car: string | number | { id: string | number }, photo: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photo: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const photos = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default photos