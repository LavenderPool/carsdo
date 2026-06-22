<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        {!! seo($SEOData ?? null) !!}

        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Open+Sans:wght@400;600;700&family=Roboto+Condensed:wght@400;700&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="{{ asset('assets/global-styles.css') }}">
        <link rel="stylesheet" href="{{ asset('new.css') }}">
        @stack('head')
    </head>
    <body>
        @php
            $hideFooterBrands = trim($__env->yieldContent('hideFooterBrands')) === '1';
        @endphp
        <div class="header-wrapper">
            <div class="zero">
                @include('layouts.header')
            </div>
        </div>
        <div class="zero">
            @yield('content')
            @include('layouts.footer', ['hideFooterBrands' => $hideFooterBrands])
        </div>
        <button class="scroll-top-button" type="button" aria-label="Наверх" data-scroll-top-button>
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">
                <path d="M8 6L12 2L16 6"/>
                <path d="M12 2V22"/>
            </svg>
            <span class="sr-only">Наверх</span>
        </button>
        <script>
            (function () {
                const fallbackSrc = @json(asset('/placeholder.png'));

                function markLoading(image) {
                    image.classList.remove('is-loaded', 'is-error');
                    image.classList.add('is-loading');
                }

                function markLoaded(image) {
                    image.classList.remove('is-loading');
                    image.classList.add('is-loaded');
                }

                function markError(image) {
                    image.classList.remove('is-loading');
                    image.classList.add('is-loaded', 'is-error');
                }

                function applyFallback(image) {
                    const nextSrc = image.dataset.carImageFallback || fallbackSrc;

                    if (!nextSrc || image.dataset.carImageFallbackApplied === 'true' || image.getAttribute('src') === nextSrc) {
                        markError(image);
                        return;
                    }

                    image.dataset.carImageFallbackApplied = 'true';
                    markLoading(image);
                    image.src = nextSrc;
                }

                function syncInitialState(image) {
                    if (!image.complete) {
                        markLoading(image);
                        return;
                    }

                    if (image.naturalWidth > 0) {
                        markLoaded(image);
                        return;
                    }

                    applyFallback(image);
                }

                function attach(image) {
                    if (!image || image.dataset.carImageAttached === 'true') {
                        return image;
                    }

                    image.dataset.carImageAttached = 'true';

                    if (!image.hasAttribute('decoding')) {
                        image.setAttribute('decoding', 'async');
                    }

                    image.addEventListener('load', function () {
                        markLoaded(image);
                    });

                    image.addEventListener('error', function () {
                        applyFallback(image);
                    });

                    syncInitialState(image);

                    return image;
                }

                function hydrate(root) {
                    (root || document).querySelectorAll('img[data-car-image]').forEach(function (image) {
                        attach(image);
                    });
                }

                function swap(image, src) {
                    if (!image || !src) {
                        return;
                    }

                    attach(image);
                    image.dataset.carImageFallbackApplied = 'false';
                    markLoading(image);
                    image.src = src;
                }

                window.CarsdoCarImage = {
                    attach: attach,
                    hydrate: hydrate,
                    swap: swap,
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        hydrate(document);
                    });
                } else {
                    hydrate(document);
                }
            })();
        </script>
        <script>
            (function () {
                const brandLogoBase = @json(asset('assets/optimized-logos'));
                const aliasMap = {
                    'baic': 'baic-motor',
                    'gac': 'gac-group',
                    'iran-khodro': 'ikco',
                    'lynk-co': 'lynk-and-co',
                };

                function normalizeSlug(value) {
                    if (!value) {
                        return '';
                    }

                    return String(value)
                        .trim()
                        .toLowerCase()
                        .replace(/[\s_]+/g, '-')
                        .replace(/[^a-z0-9-]/g, '')
                        .replace(/-+/g, '-')
                        .replace(/^-|-$/g, '');
                }

                function getBrandLogoBySlug(slug) {
                    const normalizedSlug = normalizeSlug(slug);

                    if (!normalizedSlug) {
                        return null;
                    }

                    const mappedSlug = aliasMap[normalizedSlug] || normalizedSlug;

                    return mappedSlug ? (brandLogoBase + '/' + mappedSlug + '.png') : null;
                }

                function markLoading(image) {
                    image.classList.remove('is-loaded', 'is-error');
                    image.classList.add('is-loading');
                }

                function markLoaded(image) {
                    image.classList.remove('is-loading', 'is-error');
                    image.classList.add('is-loaded');
                }

                function markError(image) {
                    image.classList.remove('is-loading', 'is-loaded');
                    image.classList.add('is-error');
                    image.removeAttribute('src');
                    image.setAttribute('aria-hidden', 'true');
                }

                function attach(image) {
                    if (!image || image.dataset.brandLogoAttached === 'true') {
                        return image;
                    }

                    image.dataset.brandLogoAttached = 'true';

                    if (!image.hasAttribute('decoding')) {
                        image.setAttribute('decoding', 'async');
                    }

                    if (!image.hasAttribute('loading')) {
                        image.setAttribute('loading', 'lazy');
                    }

                    image.addEventListener('load', function () {
                        markLoaded(image);
                    });

                    image.addEventListener('error', function () {
                        markError(image);
                    });

                    return image;
                }

                function load(image) {
                    if (!image || image.dataset.brandLogoLoaded === 'true') {
                        return;
                    }

                    const slug = image.dataset.brandSlug || '';
                    const source = getBrandLogoBySlug(slug);

                    if (!source) {
                        markError(image);
                        image.dataset.brandLogoLoaded = 'true';
                        return;
                    }

                    image.dataset.brandLogoLoaded = 'true';
                    markLoading(image);
                    image.src = source;
                }

                function hydrate(root) {
                    const images = (root || document).querySelectorAll('img[data-brand-logo]');

                    if (!images.length) {
                        return;
                    }

                    const supportsIntersection = 'IntersectionObserver' in window;
                    let observer = null;

                    if (supportsIntersection) {
                        observer = new IntersectionObserver(function (entries, currentObserver) {
                            entries.forEach(function (entry) {
                                if (!entry.isIntersecting) {
                                    return;
                                }

                                load(entry.target);
                                currentObserver.unobserve(entry.target);
                            });
                        }, {
                            rootMargin: '120px 0px',
                        });
                    }

                    images.forEach(function (image) {
                        attach(image);

                        if (supportsIntersection) {
                            observer.observe(image);
                            return;
                        }

                        load(image);
                    });
                }

                window.CarsdoBrandLogo = {
                    attach: attach,
                    getBrandLogoBySlug: getBrandLogoBySlug,
                    hydrate: hydrate,
                    load: load,
                };

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', function () {
                        hydrate(document);
                    });
                } else {
                    hydrate(document);
                }
            })();
        </script>
        <script>
            (function () {
                function normalize(value) {
                    return String(value || '')
                        .trim()
                        .toLowerCase();
                }

                function setupBrandFilter(filterRoot) {
                    if (!filterRoot || filterRoot.dataset.brandFilterReady === 'true') {
                        return null;
                    }

                    const trigger = filterRoot.querySelector('[data-brand-filter-trigger]');
                    const panel = filterRoot.querySelector('[data-brand-filter-panel]');
                    const searchInput = filterRoot.querySelector('[data-brand-filter-search]');
                    const emptyState = filterRoot.querySelector('[data-brand-filter-empty]');
                    const options = Array.prototype.slice.call(filterRoot.querySelectorAll('[data-brand-filter-option]'));

                    if (!trigger || !panel || !searchInput || !options.length) {
                        return null;
                    }

                    filterRoot.dataset.brandFilterReady = 'true';

                    function applyFilter() {
                        const query = normalize(searchInput.value);
                        let visibleCount = 0;

                        options.forEach(function (option) {
                            const item = option.closest('.brand-filter-select__item');

                            if (!item) {
                                return;
                            }

                            const isFixed = option.dataset.brandFilterFixed === 'latest';
                            const haystack = normalize(option.dataset.searchText || option.textContent);
                            const isMatch = isFixed || !query || haystack.indexOf(query) !== -1;

                            item.hidden = !isMatch;

                            if (isMatch) {
                                visibleCount += 1;
                            }
                        });

                        if (emptyState) {
                            emptyState.hidden = visibleCount > 0;
                        }
                    }

                    function closeFilter() {
                        trigger.setAttribute('aria-expanded', 'false');
                        panel.hidden = true;
                        filterRoot.classList.remove('is-open');
                    }

                    function openFilter() {
                        trigger.setAttribute('aria-expanded', 'true');
                        panel.hidden = false;
                        filterRoot.classList.add('is-open');
                        applyFilter();
                        searchInput.focus();
                        searchInput.select();
                    }

                    function toggleFilter() {
                        const isOpen = filterRoot.classList.contains('is-open');

                        if (isOpen) {
                            closeFilter();
                            return;
                        }

                        instances.forEach(function (instance) {
                            if (instance !== api) {
                                instance.close();
                            }
                        });

                        openFilter();
                    }

                    trigger.addEventListener('click', function (event) {
                        event.preventDefault();
                        toggleFilter();
                    });

                    filterRoot.addEventListener('keydown', function (event) {
                        if (event.key !== 'Escape' || !filterRoot.classList.contains('is-open')) {
                            return;
                        }

                        event.preventDefault();
                        event.stopPropagation();
                        closeFilter();
                        trigger.focus();
                    });

                    panel.addEventListener('click', function (event) {
                        const option = event.target.closest('[data-brand-filter-option]');

                        if (!option) {
                            return;
                        }

                        const nextUrl = option.dataset.url;

                        if (!nextUrl) {
                            return;
                        }

                        window.location.href = nextUrl;
                    });

                    searchInput.addEventListener('input', function () {
                        applyFilter();
                    });

                    applyFilter();
                    closeFilter();

                    const api = {
                        close: closeFilter,
                        root: filterRoot,
                    };

                    return api;
                }

                const instances = [];

                function hydrate() {
                    const filters = document.querySelectorAll('[data-brand-filter-select]');

                    filters.forEach(function (filterRoot) {
                        const instance = setupBrandFilter(filterRoot);

                        if (!instance) {
                            return;
                        }

                        instances.push(instance);
                    });
                }

                document.addEventListener('click', function (event) {
                    instances.forEach(function (instance) {
                        if (instance.root.contains(event.target)) {
                            return;
                        }

                        instance.close();
                    });
                });

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', hydrate);
                } else {
                    hydrate();
                }
            })();
        </script>
        <script>
            (function () {
                const button = document.querySelector('[data-scroll-top-button]');

                if (!button) {
                    return;
                }

                function toggleButton() {
                    const threshold = window.innerHeight * 1.1;
                    const shouldShow = window.scrollY > threshold;
                    button.classList.toggle('is-visible', shouldShow);
                }

                button.addEventListener('click', function () {
                    window.scrollTo({
                        top: 0,
                        behavior: 'smooth',
                    });
                });

                window.addEventListener('scroll', toggleButton, { passive: true });
                window.addEventListener('resize', toggleButton);
                toggleButton();
            })();
        </script>
    </body>
</html>
