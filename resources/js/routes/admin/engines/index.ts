import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../../wayfinder'
import importMethod from './import'
/**
* @see \App\Http\Controllers\Admin\EngineController::index
* @see app/Http/Controllers/Admin/EngineController.php:17
* @route '/admin/engines'
*/
export const index = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

index.definition = {
    methods: ["get","head"],
    url: '/admin/engines',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::index
* @see app/Http/Controllers/Admin/EngineController.php:17
* @route '/admin/engines'
*/
index.url = (options?: RouteQueryOptions) => {
    return index.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::index
* @see app/Http/Controllers/Admin/EngineController.php:17
* @route '/admin/engines'
*/
index.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: index.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::index
* @see app/Http/Controllers/Admin/EngineController.php:17
* @route '/admin/engines'
*/
index.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: index.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::create
* @see app/Http/Controllers/Admin/EngineController.php:62
* @route '/admin/engines/create'
*/
export const create = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

create.definition = {
    methods: ["get","head"],
    url: '/admin/engines/create',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::create
* @see app/Http/Controllers/Admin/EngineController.php:62
* @route '/admin/engines/create'
*/
create.url = (options?: RouteQueryOptions) => {
    return create.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::create
* @see app/Http/Controllers/Admin/EngineController.php:62
* @route '/admin/engines/create'
*/
create.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: create.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::create
* @see app/Http/Controllers/Admin/EngineController.php:62
* @route '/admin/engines/create'
*/
create.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: create.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::store
* @see app/Http/Controllers/Admin/EngineController.php:69
* @route '/admin/engines'
*/
export const store = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

store.definition = {
    methods: ["post"],
    url: '/admin/engines',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::store
* @see app/Http/Controllers/Admin/EngineController.php:69
* @route '/admin/engines'
*/
store.url = (options?: RouteQueryOptions) => {
    return store.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::store
* @see app/Http/Controllers/Admin/EngineController.php:69
* @route '/admin/engines'
*/
store.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: store.url(options),
    method: 'post',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::edit
* @see app/Http/Controllers/Admin/EngineController.php:78
* @route '/admin/engines/{engine}/edit'
*/
export const edit = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

edit.definition = {
    methods: ["get","head"],
    url: '/admin/engines/{engine}/edit',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::edit
* @see app/Http/Controllers/Admin/EngineController.php:78
* @route '/admin/engines/{engine}/edit'
*/
edit.url = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { engine: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { engine: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            engine: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        engine: typeof args.engine === 'object'
        ? args.engine.id
        : args.engine,
    }

    return edit.definition.url
            .replace('{engine}', parsedArgs.engine.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::edit
* @see app/Http/Controllers/Admin/EngineController.php:78
* @route '/admin/engines/{engine}/edit'
*/
edit.get = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: edit.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::edit
* @see app/Http/Controllers/Admin/EngineController.php:78
* @route '/admin/engines/{engine}/edit'
*/
edit.head = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: edit.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::update
* @see app/Http/Controllers/Admin/EngineController.php:91
* @route '/admin/engines/{engine}'
*/
export const update = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

update.definition = {
    methods: ["put","patch"],
    url: '/admin/engines/{engine}',
} satisfies RouteDefinition<["put","patch"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::update
* @see app/Http/Controllers/Admin/EngineController.php:91
* @route '/admin/engines/{engine}'
*/
update.url = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { engine: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { engine: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            engine: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        engine: typeof args.engine === 'object'
        ? args.engine.id
        : args.engine,
    }

    return update.definition.url
            .replace('{engine}', parsedArgs.engine.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::update
* @see app/Http/Controllers/Admin/EngineController.php:91
* @route '/admin/engines/{engine}'
*/
update.put = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(args, options),
    method: 'put',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::update
* @see app/Http/Controllers/Admin/EngineController.php:91
* @route '/admin/engines/{engine}'
*/
update.patch = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'patch'> => ({
    url: update.url(args, options),
    method: 'patch',
})

/**
* @see \App\Http\Controllers\Admin\EngineController::destroy
* @see app/Http/Controllers/Admin/EngineController.php:100
* @route '/admin/engines/{engine}'
*/
export const destroy = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

destroy.definition = {
    methods: ["delete"],
    url: '/admin/engines/{engine}',
} satisfies RouteDefinition<["delete"]>

/**
* @see \App\Http\Controllers\Admin\EngineController::destroy
* @see app/Http/Controllers/Admin/EngineController.php:100
* @route '/admin/engines/{engine}'
*/
destroy.url = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions) => {
    if (typeof args === 'string' || typeof args === 'number') {
        args = { engine: args }
    }

    if (typeof args === 'object' && !Array.isArray(args) && 'id' in args) {
        args = { engine: args.id }
    }

    if (Array.isArray(args)) {
        args = {
            engine: args[0],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        engine: typeof args.engine === 'object'
        ? args.engine.id
        : args.engine,
    }

    return destroy.definition.url
            .replace('{engine}', parsedArgs.engine.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\EngineController::destroy
* @see app/Http/Controllers/Admin/EngineController.php:100
* @route '/admin/engines/{engine}'
*/
destroy.delete = (args: { engine: number | { id: number } } | [engine: number | { id: number } ] | number | { id: number }, options?: RouteQueryOptions): RouteDefinition<'delete'> => ({
    url: destroy.url(args, options),
    method: 'delete',
})

const engines = {
    index: Object.assign(index, index),
    create: Object.assign(create, create),
    store: Object.assign(store, store),
    edit: Object.assign(edit, edit),
    update: Object.assign(update, update),
    destroy: Object.assign(destroy, destroy),
    import: Object.assign(importMethod, importMethod),
}

export default engines