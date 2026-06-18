import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::index
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:17
* @route '/admin/cars/{car}/photo-groups'
*/
export const index = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photo-groups',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::index
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:17
* @route '/admin/cars/{car}/photo-groups'
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
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::index
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:17
* @route '/admin/cars/{car}/photo-groups'
*/
index.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::index
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:17
* @route '/admin/cars/{car}/photo-groups'
*/
index.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::create
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:44
* @route '/admin/cars/{car}/photo-groups/create'
*/
export const create = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photo-groups/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::create
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:44
* @route '/admin/cars/{car}/photo-groups/create'
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
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::create
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:44
* @route '/admin/cars/{car}/photo-groups/create'
*/
create.get = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::create
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:44
* @route '/admin/cars/{car}/photo-groups/create'
*/
create.head = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::store
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:64
* @route '/admin/cars/{car}/photo-groups'
*/
export const store = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/photo-groups',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::store
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:64
* @route '/admin/cars/{car}/photo-groups'
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
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::store
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:64
* @route '/admin/cars/{car}/photo-groups'
*/
store.post = (args: { car: string | number | { id: string | number } } | [car: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::edit
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:73
* @route '/admin/cars/{car}/photo-groups/{photoGroup}/edit'
*/
export const edit = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/photo-groups/{photoGroup}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::edit
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:73
* @route '/admin/cars/{car}/photo-groups/{photoGroup}/edit'
*/
edit.url = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photoGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photoGroup: typeof args.photoGroup === 'object'
        ? args.photoGroup.id
        : args.photoGroup,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photoGroup}', parsedArgs.photoGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::edit
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:73
* @route '/admin/cars/{car}/photo-groups/{photoGroup}/edit'
*/
edit.get = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::edit
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:73
* @route '/admin/cars/{car}/photo-groups/{photoGroup}/edit'
*/
edit.head = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::update
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:95
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
export const update = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/photo-groups/{photoGroup}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::update
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:95
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
update.url = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photoGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photoGroup: typeof args.photoGroup === 'object'
        ? args.photoGroup.id
        : args.photoGroup,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photoGroup}', parsedArgs.photoGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::update
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:95
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
update.put = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::update
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:95
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
update.patch = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::destroy
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:105
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
export const destroy = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/photo-groups/{photoGroup}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::destroy
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:105
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
destroy.url = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            photoGroup: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        photoGroup: typeof args.photoGroup === 'object'
        ? args.photoGroup.id
        : args.photoGroup,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{photoGroup}', parsedArgs.photoGroup.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarPhotoGroupController::destroy
* @see app/Http/Controllers/Admin/CarPhotoGroupController.php:105
* @route '/admin/cars/{car}/photo-groups/{photoGroup}'
*/
destroy.delete = (args: { car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } } | [car: string | number | { id: string | number }, photoGroup: string | number | { id: string | number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarPhotoGroupController = { index, create, store, edit, update, destroy }

export default CarPhotoGroupController