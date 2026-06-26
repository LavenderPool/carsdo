import { queryParams, type RouteQueryOptions, type RouteDefinition } from './../../../../wayfinder'
/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
export const apply = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

apply.definition = {
    methods: ["post"],
    url: '/admin/danger/webp-convert',
} satisfies RouteDefinition<["post"]>

/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
apply.url = (options?: RouteQueryOptions) => {
    return apply.definition.url + queryParams(options)
}

/**
* @see \App\Http\Controllers\Admin\DangerController::apply
* @see app/Http/Controllers/Admin/DangerController.php:113
* @route '/admin/danger/webp-convert'
*/
apply.post = (options?: RouteQueryOptions): RouteDefinition<'post'> => ({
    url: apply.url(options),
    method: 'post',
})

const webpConvert = {
    apply: Object.assign(apply, apply),
}

export default webpConvert