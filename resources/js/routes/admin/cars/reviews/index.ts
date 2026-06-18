import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarReviewController::index
* @see app/Http/Controllers/Admin/CarReviewController.php:16
* @route '/admin/cars/{car}/reviews'
*/
export const index = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::index
* @see app/Http/Controllers/Admin/CarReviewController.php:16
* @route '/admin/cars/{car}/reviews'
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
* @see \App\Http\Controllers\Admin\CarReviewController::index
* @see app/Http/Controllers/Admin/CarReviewController.php:16
* @route '/admin/cars/{car}/reviews'
*/
index.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::index
* @see app/Http/Controllers/Admin/CarReviewController.php:16
* @route '/admin/cars/{car}/reviews'
*/
index.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::create
* @see app/Http/Controllers/Admin/CarReviewController.php:47
* @route '/admin/cars/{car}/reviews/create'
*/
export const create = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/reviews/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::create
* @see app/Http/Controllers/Admin/CarReviewController.php:47
* @route '/admin/cars/{car}/reviews/create'
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
* @see \App\Http\Controllers\Admin\CarReviewController::create
* @see app/Http/Controllers/Admin/CarReviewController.php:47
* @route '/admin/cars/{car}/reviews/create'
*/
create.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::create
* @see app/Http/Controllers/Admin/CarReviewController.php:47
* @route '/admin/cars/{car}/reviews/create'
*/
create.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::store
* @see app/Http/Controllers/Admin/CarReviewController.php:71
* @route '/admin/cars/{car}/reviews'
*/
export const store = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::store
* @see app/Http/Controllers/Admin/CarReviewController.php:71
* @route '/admin/cars/{car}/reviews'
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
* @see \App\Http\Controllers\Admin\CarReviewController::store
* @see app/Http/Controllers/Admin/CarReviewController.php:71
* @route '/admin/cars/{car}/reviews'
*/
store.post = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::edit
* @see app/Http/Controllers/Admin/CarReviewController.php:80
* @route '/admin/cars/{car}/reviews/{review}/edit'
*/
export const edit = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/reviews/{review}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::edit
* @see app/Http/Controllers/Admin/CarReviewController.php:80
* @route '/admin/cars/{car}/reviews/{review}/edit'
*/
edit.url = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            review: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarReviewController::edit
* @see app/Http/Controllers/Admin/CarReviewController.php:80
* @route '/admin/cars/{car}/reviews/{review}/edit'
*/
edit.get = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::edit
* @see app/Http/Controllers/Admin/CarReviewController.php:80
* @route '/admin/cars/{car}/reviews/{review}/edit'
*/
edit.head = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::update
* @see app/Http/Controllers/Admin/CarReviewController.php:106
* @route '/admin/cars/{car}/reviews/{review}'
*/
export const update = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/reviews/{review}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::update
* @see app/Http/Controllers/Admin/CarReviewController.php:106
* @route '/admin/cars/{car}/reviews/{review}'
*/
update.url = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            review: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarReviewController::update
* @see app/Http/Controllers/Admin/CarReviewController.php:106
* @route '/admin/cars/{car}/reviews/{review}'
*/
update.put = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::update
* @see app/Http/Controllers/Admin/CarReviewController.php:106
* @route '/admin/cars/{car}/reviews/{review}'
*/
update.patch = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarReviewController::destroy
* @see app/Http/Controllers/Admin/CarReviewController.php:116
* @route '/admin/cars/{car}/reviews/{review}'
*/
export const destroy = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/reviews/{review}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarReviewController::destroy
* @see app/Http/Controllers/Admin/CarReviewController.php:116
* @route '/admin/cars/{car}/reviews/{review}'
*/
destroy.url = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            review: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        review: typeof args.review === 'object'
        ? args.review.id
        : args.review,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{review}', parsedArgs.review.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarReviewController::destroy
* @see app/Http/Controllers/Admin/CarReviewController.php:116
* @route '/admin/cars/{car}/reviews/{review}'
*/
destroy.delete = (args: { car: number | { id: number }, review: number | { id: number } } | [car: number | { id: number }, review: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const reviews = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
}

export default reviews