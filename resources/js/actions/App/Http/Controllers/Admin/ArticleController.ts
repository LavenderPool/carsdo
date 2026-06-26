import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\ArticleController::index
* @see app/Http/Controllers/Admin/ArticleController.php:19
* @route '/admin/articles'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/articles',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::index
* @see app/Http/Controllers/Admin/ArticleController.php:19
* @route '/admin/articles'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::index
* @see app/Http/Controllers/Admin/ArticleController.php:19
* @route '/admin/articles'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::index
* @see app/Http/Controllers/Admin/ArticleController.php:19
* @route '/admin/articles'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::create
* @see app/Http/Controllers/Admin/ArticleController.php:64
* @route '/admin/articles/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/articles/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::create
* @see app/Http/Controllers/Admin/ArticleController.php:64
* @route '/admin/articles/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::create
* @see app/Http/Controllers/Admin/ArticleController.php:64
* @route '/admin/articles/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::create
* @see app/Http/Controllers/Admin/ArticleController.php:64
* @route '/admin/articles/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::store
* @see app/Http/Controllers/Admin/ArticleController.php:69
* @route '/admin/articles'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/articles',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::store
* @see app/Http/Controllers/Admin/ArticleController.php:69
* @route '/admin/articles'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::store
* @see app/Http/Controllers/Admin/ArticleController.php:69
* @route '/admin/articles'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::edit
* @see app/Http/Controllers/Admin/ArticleController.php:92
* @route '/admin/articles/{article}/edit'
*/
export const edit = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/articles/{article}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::edit
* @see app/Http/Controllers/Admin/ArticleController.php:92
* @route '/admin/articles/{article}/edit'
*/
edit.url = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { article: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { article: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            article: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        article: typeof args.article === 'object'
        ? args.article.id
        : args.article,
    }

    return edit.definition.url
            .replace('{article}', parsedArgs.article.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::edit
* @see app/Http/Controllers/Admin/ArticleController.php:92
* @route '/admin/articles/{article}/edit'
*/
edit.get = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::edit
* @see app/Http/Controllers/Admin/ArticleController.php:92
* @route '/admin/articles/{article}/edit'
*/
edit.head = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::update
* @see app/Http/Controllers/Admin/ArticleController.php:111
* @route '/admin/articles/{article}'
*/
export const update = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/articles/{article}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::update
* @see app/Http/Controllers/Admin/ArticleController.php:111
* @route '/admin/articles/{article}'
*/
update.url = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { article: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { article: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            article: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        article: typeof args.article === 'object'
        ? args.article.id
        : args.article,
    }

    return update.definition.url
            .replace('{article}', parsedArgs.article.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::update
* @see app/Http/Controllers/Admin/ArticleController.php:111
* @route '/admin/articles/{article}'
*/
update.put = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::update
* @see app/Http/Controllers/Admin/ArticleController.php:111
* @route '/admin/articles/{article}'
*/
update.patch = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\ArticleController::destroy
* @see app/Http/Controllers/Admin/ArticleController.php:142
* @route '/admin/articles/{article}'
*/
export const destroy = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/articles/{article}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\ArticleController::destroy
* @see app/Http/Controllers/Admin/ArticleController.php:142
* @route '/admin/articles/{article}'
*/
destroy.url = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { article: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { article: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            article: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        article: typeof args.article === 'object'
        ? args.article.id
        : args.article,
    }

    return destroy.definition.url
            .replace('{article}', parsedArgs.article.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\ArticleController::destroy
* @see app/Http/Controllers/Admin/ArticleController.php:142
* @route '/admin/articles/{article}'
*/
destroy.delete = (args: { article: number | { id: number } } | [article: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const ArticleController = { index, create, store, edit, update, destroy }

export default ArticleController