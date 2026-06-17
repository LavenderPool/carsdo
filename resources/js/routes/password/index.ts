import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::confirm
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:18
* @route '/confirm-password'
*/
export const confirm = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})

confirm.definition = {
    methods: ["get","head"],
    url: '/confirm-password',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::confirm
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:18
* @route '/confirm-password'
*/
confirm.url = (options?: RouteQueryOptions) => {
    return confirm.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::confirm
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:18
* @route '/confirm-password'
*/
confirm.get = (options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: confirm.url(options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Auth\ConfirmablePasswordController::confirm
* @see app/Http/Controllers/Auth/ConfirmablePasswordController.php:18
* @route '/confirm-password'
*/
confirm.head = (options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: confirm.url(options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Auth\PasswordController::update
* @see app/Http/Controllers/Auth/PasswordController.php:16
* @route '/password'
*/
export const update = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

update.definition = {
    methods: ["put"],
    url: '/password',
} satisfies RouteDefinition<["put"]>

/**
* @see \App\Http\Controllers\Auth\PasswordController::update
* @see app/Http/Controllers/Auth/PasswordController.php:16
* @route '/password'
*/
update.url = (options?: RouteQueryOptions) => {
    return update.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Auth\PasswordController::update
* @see app/Http/Controllers/Auth/PasswordController.php:16
* @route '/password'
*/
update.put = (options?: RouteQueryOptions): RouteDefinition<'put'> => ({
    url: update.url(options),
    method: 'put',
})

const password = {
    confirm: Object.assign(confirm, confirm),
    update: Object.assign(update, update),
}

export default password