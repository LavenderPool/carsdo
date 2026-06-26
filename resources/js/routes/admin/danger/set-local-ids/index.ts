import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
export const apply = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/admin/danger/set-local-ids',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
apply.url = (options?: RouteQueryOptions) => {
    return apply.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:53
* @route '/admin/danger/set-local-ids'
*/
apply.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

const setLocalIds = {
    apply: Object.assign(apply, apply),
}

export default setLocalIds