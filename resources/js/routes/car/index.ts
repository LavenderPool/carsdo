import { queryParams, type RouteQueryOptions, type RouteDefinition, applyUrlDefaults } from './../../wayfinder'
/**
* @see \App\Http\Controllers\Site\CarController::testDrive
* @see app/Http/Controllers/Site/CarController.php:52
* @route '/{brand}/{car}/test-drive'
*/
export const testDrive = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: testDrive.url(args, options),
    method: 'get',
})

testDrive.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/test-drive',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::testDrive
* @see app/Http/Controllers/Site/CarController.php:52
* @route '/{brand}/{car}/test-drive'
*/
testDrive.url = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
    }

    return testDrive.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::testDrive
* @see app/Http/Controllers/Site/CarController.php:52
* @route '/{brand}/{car}/test-drive'
*/
testDrive.get = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: testDrive.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::testDrive
* @see app/Http/Controllers/Site/CarController.php:52
* @route '/{brand}/{car}/test-drive'
*/
testDrive.head = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: testDrive.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::crashTest
* @see app/Http/Controllers/Site/CarController.php:79
* @route '/{brand}/{car}/crash-test'
*/
export const crashTest = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: crashTest.url(args, options),
    method: 'get',
})

crashTest.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/crash-test',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::crashTest
* @see app/Http/Controllers/Site/CarController.php:79
* @route '/{brand}/{car}/crash-test'
*/
crashTest.url = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
    }

    return crashTest.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::crashTest
* @see app/Http/Controllers/Site/CarController.php:79
* @route '/{brand}/{car}/crash-test'
*/
crashTest.get = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: crashTest.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::crashTest
* @see app/Http/Controllers/Site/CarController.php:79
* @route '/{brand}/{car}/crash-test'
*/
crashTest.head = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: crashTest.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::reviews
* @see app/Http/Controllers/Site/CarController.php:106
* @route '/{brand}/{car}/reviews'
*/
export const reviews = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviews.url(args, options),
    method: 'get',
})

reviews.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/reviews',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::reviews
* @see app/Http/Controllers/Site/CarController.php:106
* @route '/{brand}/{car}/reviews'
*/
reviews.url = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
    }

    return reviews.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::reviews
* @see app/Http/Controllers/Site/CarController.php:106
* @route '/{brand}/{car}/reviews'
*/
reviews.get = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: reviews.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::reviews
* @see app/Http/Controllers/Site/CarController.php:106
* @route '/{brand}/{car}/reviews'
*/
reviews.head = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: reviews.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::photo
* @see app/Http/Controllers/Site/CarController.php:131
* @route '/{brand}/{car}/photo'
*/
export const photo = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: photo.url(args, options),
    method: 'get',
})

photo.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/photo',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::photo
* @see app/Http/Controllers/Site/CarController.php:131
* @route '/{brand}/{car}/photo'
*/
photo.url = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
    }

    return photo.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::photo
* @see app/Http/Controllers/Site/CarController.php:131
* @route '/{brand}/{car}/photo'
*/
photo.get = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: photo.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::photo
* @see app/Http/Controllers/Site/CarController.php:131
* @route '/{brand}/{car}/photo'
*/
photo.head = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: photo.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::equipment
* @see app/Http/Controllers/Site/CarController.php:161
* @route '/{brand}/{car}/equipment-{localId}'
*/
export const equipment = (args: { brand: string | { slug: string }, car: string | { slug: string }, localId: string | number } | [brand: string | { slug: string }, car: string | { slug: string }, localId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: equipment.url(args, options),
    method: 'get',
})

equipment.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/equipment-{localId}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::equipment
* @see app/Http/Controllers/Site/CarController.php:161
* @route '/{brand}/{car}/equipment-{localId}'
*/
equipment.url = (args: { brand: string | { slug: string }, car: string | { slug: string }, localId: string | number } | [brand: string | { slug: string }, car: string | { slug: string }, localId: string | number ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
            localId: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
        localId: args.localId,
    }

    return equipment.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace('{localId}', parsedArgs.localId.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::equipment
* @see app/Http/Controllers/Site/CarController.php:161
* @route '/{brand}/{car}/equipment-{localId}'
*/
equipment.get = (args: { brand: string | { slug: string }, car: string | { slug: string }, localId: string | number } | [brand: string | { slug: string }, car: string | { slug: string }, localId: string | number ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: equipment.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::equipment
* @see app/Http/Controllers/Site/CarController.php:161
* @route '/{brand}/{car}/equipment-{localId}'
*/
equipment.head = (args: { brand: string | { slug: string }, car: string | { slug: string }, localId: string | number } | [brand: string | { slug: string }, car: string | { slug: string }, localId: string | number ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: equipment.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::dealer
* @see app/Http/Controllers/Site/CarController.php:222
* @route '/{brand}/{car}/{city}'
*/
export const dealer = (args: { brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dealer.url(args, options),
    method: 'get',
})

dealer.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}/{city}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::dealer
* @see app/Http/Controllers/Site/CarController.php:222
* @route '/{brand}/{car}/{city}'
*/
dealer.url = (args: { brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
            city: args[2],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
        city: typeof args.city === 'object'
        ? args.city.slug
        : args.city,
    }

    return dealer.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace('{city}', parsedArgs.city.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::dealer
* @see app/Http/Controllers/Site/CarController.php:222
* @route '/{brand}/{car}/{city}'
*/
dealer.get = (args: { brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: dealer.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::dealer
* @see app/Http/Controllers/Site/CarController.php:222
* @route '/{brand}/{car}/{city}'
*/
dealer.head = (args: { brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string }, city: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: dealer.url(args, options),
    method: 'head',
})

/**
* @see \App\Http\Controllers\Site\CarController::show
* @see app/Http/Controllers/Site/CarController.php:15
* @route '/{brand}/{car}'
*/
export const show = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

show.definition = {
    methods: ["get","head"],
    url: '/{brand}/{car}',
} satisfies RouteDefinition<["get","head"]>

/**
* @see \App\Http\Controllers\Site\CarController::show
* @see app/Http/Controllers/Site/CarController.php:15
* @route '/{brand}/{car}'
*/
show.url = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions) => {
    if (Array.isArray(args)) {
        args = {
            brand: args[0],
            car: args[1],
        }
    }

    args = applyUrlDefaults(args)

    const parsedArgs = {
        brand: typeof args.brand === 'object'
        ? args.brand.slug
        : args.brand,
        car: typeof args.car === 'object'
        ? args.car.slug
        : args.car,
    }

    return show.definition.url
            .replace('{brand}', parsedArgs.brand.toString())
            .replace('{car}', parsedArgs.car.toString())
            .replace(/\/+$/, '') + queryParams(options)
}

/**
* @see \App\Http\Controllers\Site\CarController::show
* @see app/Http/Controllers/Site/CarController.php:15
* @route '/{brand}/{car}'
*/
show.get = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'get'> => ({
    url: show.url(args, options),
    method: 'get',
})

/**
* @see \App\Http\Controllers\Site\CarController::show
* @see app/Http/Controllers/Site/CarController.php:15
* @route '/{brand}/{car}'
*/
show.head = (args: { brand: string | { slug: string }, car: string | { slug: string } } | [brand: string | { slug: string }, car: string | { slug: string } ], options?: RouteQueryOptions): RouteDefinition<'head'> => ({
    url: show.url(args, options),
    method: 'head',
})

const car = {
    testDrive: Object.assign(testDrive, testDrive),
    crashTest: Object.assign(crashTest, crashTest),
    reviews: Object.assign(reviews, reviews),
    photo: Object.assign(photo, photo),
    equipment: Object.assign(equipment, equipment),
    dealer: Object.assign(dealer, dealer),
    show: Object.assign(show, show),
}

export default car