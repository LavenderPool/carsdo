import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\PageController::index
* @see app/Http/Controllers/Admin/PageController.php:17
* @route '/admin/pages'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/pages',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\PageController::index
* @see app/Http/Controllers/Admin/PageController.php:17
* @route '/admin/pages'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::index
* @see app/Http/Controllers/Admin/PageController.php:17
* @route '/admin/pages'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PageController::index
* @see app/Http/Controllers/Admin/PageController.php:17
* @route '/admin/pages'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\PageController::create
* @see app/Http/Controllers/Admin/PageController.php:63
* @route '/admin/pages/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/pages/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\PageController::create
* @see app/Http/Controllers/Admin/PageController.php:63
* @route '/admin/pages/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::create
* @see app/Http/Controllers/Admin/PageController.php:63
* @route '/admin/pages/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PageController::create
* @see app/Http/Controllers/Admin/PageController.php:63
* @route '/admin/pages/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\PageController::store
* @see app/Http/Controllers/Admin/PageController.php:68
* @route '/admin/pages'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/pages',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\PageController::store
* @see app/Http/Controllers/Admin/PageController.php:68
* @route '/admin/pages'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::store
* @see app/Http/Controllers/Admin/PageController.php:68
* @route '/admin/pages'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\PageController::edit
* @see app/Http/Controllers/Admin/PageController.php:77
* @route '/admin/pages/{page}/edit'
*/
export const edit = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/pages/{page}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\PageController::edit
* @see app/Http/Controllers/Admin/PageController.php:77
* @route '/admin/pages/{page}/edit'
*/
edit.url = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { page: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { page: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            page: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        page: typeof args.page === 'object'
        ? args.page.id
        : args.page,
    }

    return edit.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::edit
* @see app/Http/Controllers/Admin/PageController.php:77
* @route '/admin/pages/{page}/edit'
*/
edit.get = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\PageController::edit
* @see app/Http/Controllers/Admin/PageController.php:77
* @route '/admin/pages/{page}/edit'
*/
edit.head = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\PageController::update
* @see app/Http/Controllers/Admin/PageController.php:96
* @route '/admin/pages/{page}'
*/
export const update = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/pages/{page}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\PageController::update
* @see app/Http/Controllers/Admin/PageController.php:96
* @route '/admin/pages/{page}'
*/
update.url = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { page: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { page: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            page: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        page: typeof args.page === 'object'
        ? args.page.id
        : args.page,
    }

    return update.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::update
* @see app/Http/Controllers/Admin/PageController.php:96
* @route '/admin/pages/{page}'
*/
update.put = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\PageController::update
* @see app/Http/Controllers/Admin/PageController.php:96
* @route '/admin/pages/{page}'
*/
update.patch = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\PageController::destroy
* @see app/Http/Controllers/Admin/PageController.php:105
* @route '/admin/pages/{page}'
*/
export const destroy = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/pages/{page}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\PageController::destroy
* @see app/Http/Controllers/Admin/PageController.php:105
* @route '/admin/pages/{page}'
*/
destroy.url = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { page: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { page: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            page: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        page: typeof args.page === 'object'
        ? args.page.id
        : args.page,
    }

    return destroy.definition.url
            .replace('{page}', parsedArgs.page.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\PageController::destroy
* @see app/Http/Controllers/Admin/PageController.php:105
* @route '/admin/pages/{page}'
*/
destroy.delete = (args: { page: number | { id: number } } | [page: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const PageController = { index, create, store, edit, update, destroy }

export default PageController