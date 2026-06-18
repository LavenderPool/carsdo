import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\DealerController::index
* @see app/Http/Controllers/Admin/DealerController.php:16
* @route '/admin/dealers'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/dealers',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::index
* @see app/Http/Controllers/Admin/DealerController.php:16
* @route '/admin/dealers'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::index
* @see app/Http/Controllers/Admin/DealerController.php:16
* @route '/admin/dealers'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::index
* @see app/Http/Controllers/Admin/DealerController.php:16
* @route '/admin/dealers'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::create
* @see app/Http/Controllers/Admin/DealerController.php:44
* @route '/admin/dealers/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/dealers/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::create
* @see app/Http/Controllers/Admin/DealerController.php:44
* @route '/admin/dealers/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::create
* @see app/Http/Controllers/Admin/DealerController.php:44
* @route '/admin/dealers/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::create
* @see app/Http/Controllers/Admin/DealerController.php:44
* @route '/admin/dealers/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::store
* @see app/Http/Controllers/Admin/DealerController.php:49
* @route '/admin/dealers'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/dealers',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::store
* @see app/Http/Controllers/Admin/DealerController.php:49
* @route '/admin/dealers'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::store
* @see app/Http/Controllers/Admin/DealerController.php:49
* @route '/admin/dealers'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::edit
* @see app/Http/Controllers/Admin/DealerController.php:58
* @route '/admin/dealers/{dealer}/edit'
*/
export const edit = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/dealers/{dealer}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::edit
* @see app/Http/Controllers/Admin/DealerController.php:58
* @route '/admin/dealers/{dealer}/edit'
*/
edit.url = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        dealer: typeof args.dealer === 'object'
        ? args.dealer.id
        : args.dealer,
    }

    return edit.definition.url
            .replace('{dealer}', parsedArgs.dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::edit
* @see app/Http/Controllers/Admin/DealerController.php:58
* @route '/admin/dealers/{dealer}/edit'
*/
edit.get = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::edit
* @see app/Http/Controllers/Admin/DealerController.php:58
* @route '/admin/dealers/{dealer}/edit'
*/
edit.head = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::update
* @see app/Http/Controllers/Admin/DealerController.php:68
* @route '/admin/dealers/{dealer}'
*/
export const update = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/dealers/{dealer}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::update
* @see app/Http/Controllers/Admin/DealerController.php:68
* @route '/admin/dealers/{dealer}'
*/
update.url = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        dealer: typeof args.dealer === 'object'
        ? args.dealer.id
        : args.dealer,
    }

    return update.definition.url
            .replace('{dealer}', parsedArgs.dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::update
* @see app/Http/Controllers/Admin/DealerController.php:68
* @route '/admin/dealers/{dealer}'
*/
update.put = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::update
* @see app/Http/Controllers/Admin/DealerController.php:68
* @route '/admin/dealers/{dealer}'
*/
update.patch = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\DealerController::destroy
* @see app/Http/Controllers/Admin/DealerController.php:77
* @route '/admin/dealers/{dealer}'
*/
export const destroy = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/dealers/{dealer}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\DealerController::destroy
* @see app/Http/Controllers/Admin/DealerController.php:77
* @route '/admin/dealers/{dealer}'
*/
destroy.url = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { dealer: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { dealer: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            dealer: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        dealer: typeof args.dealer === 'object'
        ? args.dealer.id
        : args.dealer,
    }

    return destroy.definition.url
            .replace('{dealer}', parsedArgs.dealer.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DealerController::destroy
* @see app/Http/Controllers/Admin/DealerController.php:77
* @route '/admin/dealers/{dealer}'
*/
destroy.delete = (args: { dealer: string | number | { id: string | number } } | [dealer: string | number | { id: string | number } ] | string | number | { id: string | number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const DealerController = { index, create, store, edit, update, destroy }

export default DealerController