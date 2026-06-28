import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::index
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:18
* @route '/admin/cars/{car}/owner-reviews'
*/
export const index = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/owner-reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::index
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:18
* @route '/admin/cars/{car}/owner-reviews'
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
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::index
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:18
* @route '/admin/cars/{car}/owner-reviews'
*/
index.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::index
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:18
* @route '/admin/cars/{car}/owner-reviews'
*/
index.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::create
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:53
* @route '/admin/cars/{car}/owner-reviews/create'
*/
export const create = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/owner-reviews/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::create
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:53
* @route '/admin/cars/{car}/owner-reviews/create'
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
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::create
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:53
* @route '/admin/cars/{car}/owner-reviews/create'
*/
create.get = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::create
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:53
* @route '/admin/cars/{car}/owner-reviews/create'
*/
create.head = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::store
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:81
* @route '/admin/cars/{car}/owner-reviews'
*/
export const store = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/cars/{car}/owner-reviews',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::store
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:81
* @route '/admin/cars/{car}/owner-reviews'
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
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::store
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:81
* @route '/admin/cars/{car}/owner-reviews'
*/
store.post = (args: { car: number | { id: number } } | [car: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(args, options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::edit
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:94
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}/edit'
*/
export const edit = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/cars/{car}/owner-reviews/{ownerReview}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::edit
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:94
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}/edit'
*/
edit.url = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            ownerReview: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        ownerReview: typeof args.ownerReview === 'object'
        ? args.ownerReview.id
        : args.ownerReview,
    }

    return edit.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{ownerReview}', parsedArgs.ownerReview.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::edit
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:94
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}/edit'
*/
edit.get = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::edit
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:94
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}/edit'
*/
edit.head = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::update
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:125
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
export const update = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/cars/{car}/owner-reviews/{ownerReview}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::update
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:125
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
update.url = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            ownerReview: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        ownerReview: typeof args.ownerReview === 'object'
        ? args.ownerReview.id
        : args.ownerReview,
    }

    return update.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{ownerReview}', parsedArgs.ownerReview.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::update
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:125
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
update.put = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::update
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:125
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
update.patch = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::destroy
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:144
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
export const destroy = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/cars/{car}/owner-reviews/{ownerReview}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::destroy
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:144
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
destroy.url = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            car: args[0],
            ownerReview: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        car: typeof args.car === 'object'
        ? args.car.id
        : args.car,
        ownerReview: typeof args.ownerReview === 'object'
        ? args.ownerReview.id
        : args.ownerReview,
    }

    return destroy.definition.url
            .replace('{car}', parsedArgs.car.toString())
            .replace('{ownerReview}', parsedArgs.ownerReview.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\CarOwnerReviewController::destroy
* @see app/Http/Controllers/Admin/CarOwnerReviewController.php:144
* @route '/admin/cars/{car}/owner-reviews/{ownerReview}'
*/
destroy.delete = (args: { car: number | { id: number }, ownerReview: number | { id: number } } | [car: number | { id: number }, ownerReview: number | { id: number } ], options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const CarOwnerReviewController = { index, create, store, edit, update, destroy }

export default CarOwnerReviewController