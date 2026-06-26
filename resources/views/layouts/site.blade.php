<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    {!! seo($SEOData ?? null) !!}

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Open+Sans:wght@400;600;700&family=Roboto+Condensed:wght@400;700&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="{{ $siteGlobalStylesUrl }}">
    <link rel="stylesheet" href="{{ $siteNewCssUrl }}">
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
        <main>
            @yield('content')
        </main>
        @include('layouts.footer', ['hideFooterBrands' => $hideFooterBrands])
    </div>
    <button class="scroll-top-button" type="button" aria-label="Наверх" data-scroll-top-button>
        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"
            focusable="false">
            <path d="M8 6L12 2L16 6" />
            <path d="M12 2V22" />
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
            function normalize(value) {
                return String(value || '').trim().toLowerCase();
            }

            function setupBrandSelect(root) {
                if (!root || root.dataset.brandSelectReady === 'true') {
                    return null;
                }

                const trigger = root.querySelector('[data-brand-select-trigger]');
                const panel = root.querySelector('[data-brand-select-panel]');
                const search = root.querySelector('[data-brand-select-search]');
                const label = root.querySelector('[data-brand-select-label]');
                const input = root.querySelector('[data-brand-select-input]');
                const emptyState = root.querySelector('[data-brand-select-empty]');
                const options = Array.prototype.slice.call(root.querySelectorAll('[data-brand-select-option]'));

                if (!trigger || !panel || !search || !label || !input || !options.length) {
                    return null;
                }

                root.dataset.brandSelectReady = 'true';

                function applyFilter() {
                    const query = normalize(search.value);
                    let visible = 0;

                    options.forEach(function (option) {
                        const item = option.closest('.brand-select__item');
                        if (!item) {
                            return;
                        }
                        const isFixed = option.dataset.brandSelectFixed === 'any';
                        const haystack = normalize(option.dataset.searchText || option.textContent);
                        const match = isFixed || !query || haystack.indexOf(query) !== -1;
                        item.hidden = !match;
                        if (match) {
                            visible += 1;
                        }
                    });

                    if (emptyState) {
                        emptyState.hidden = visible > 0;
                    }
                }

                function close() {
                    trigger.setAttribute('aria-expanded', 'false');
                    panel.hidden = true;
                    root.classList.remove('is-open');
                }

                function open() {
                    instances.forEach(function (instance) {
                        if (instance.root !== root) {
                            instance.close();
                        }
                    });
                    trigger.setAttribute('aria-expanded', 'true');
                    panel.hidden = false;
                    root.classList.add('is-open');
                    applyFilter();
                    search.focus();
                    search.select();
                }

                trigger.addEventListener('click', function (event) {
                    event.preventDefault();
                    if (root.classList.contains('is-open')) {
                        close();
                    } else {
                        open();
                    }
                });

                panel.addEventListener('click', function (event) {
                    const option = event.target.closest('[data-brand-select-option]');
                    if (!option) {
                        return;
                    }

                    input.value = option.dataset.value || '';
                    input.dispatchEvent(new Event('input', { bubbles: true }));
                    input.dispatchEvent(new Event('change', { bubbles: true }));
                    label.textContent = option.dataset.label || '';
                    label.classList.toggle('is-placeholder', !option.dataset.value);

                    options.forEach(function (other) {
                        const active = other === option;
                        other.classList.toggle('brand-select__option--active', active);
                        other.setAttribute('aria-selected', active ? 'true' : 'false');
                    });

                    close();
                    trigger.focus();
                });

                search.addEventListener('input', applyFilter);

                root.addEventListener('keydown', function (event) {
                    if (event.key === 'Escape' && root.classList.contains('is-open')) {
                        event.preventDefault();
                        event.stopPropagation();
                        close();
                        trigger.focus();
                    }
                });

                applyFilter();
                close();

                return { root: root, close: close };
            }

            const instances = [];

            function hydrate() {
                document.querySelectorAll('[data-brand-select]').forEach(function (root) {
                    const instance = setupBrandSelect(root);
                    if (instance) {
                        instances.push(instance);
                    }
                });
            }

            document.addEventListener('click', function (event) {
                instances.forEach(function (instance) {
                    if (!instance.root.contains(event.target)) {
                        instance.close();
                    }
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
            const header = document.querySelector('.header-wrapper');

            if (!header) {
                return;
            }

            function syncHeaderOffset() {
                const isSticky = window.getComputedStyle(header).position === 'sticky';
                const offset = isSticky ? header.getBoundingClientRect().height : 0;
                document.documentElement.style.setProperty('--header-sticky-offset', offset + 'px');
            }

            syncHeaderOffset();
            window.addEventListener('resize', syncHeaderOffset, { passive: true });
        })();
    </script>
    <script>
        (function () {
            const filters = document.querySelector('[data-search-filters]');

            if (!filters) {
                return;
            }

            const openTriggers = document.querySelectorAll('[data-search-filters-open]');
            const closeTriggers = filters.querySelectorAll('[data-search-filters-close]');
            const desktopQuery = window.matchMedia('(min-width: 1000px)');
            let lastFocused = null;

            function open() {
                if (desktopQuery.matches) {
                    return;
                }

                lastFocused = document.activeElement;
                filters.classList.add('is-open');
                document.body.style.overflow = 'hidden';
                openTriggers.forEach(function (trigger) {
                    trigger.setAttribute('aria-expanded', 'true');
                });

                const closeButton = filters.querySelector('[data-search-filters-close]:not(.search-page__filters-backdrop)');
                if (closeButton) {
                    closeButton.focus();
                }
            }

            function close() {
                filters.classList.remove('is-open');
                document.body.style.overflow = '';
                openTriggers.forEach(function (trigger) {
                    trigger.setAttribute('aria-expanded', 'false');
                });

                if (lastFocused && typeof lastFocused.focus === 'function') {
                    lastFocused.focus();
                }
            }

            openTriggers.forEach(function (trigger) {
                trigger.addEventListener('click', open);
            });

            closeTriggers.forEach(function (trigger) {
                trigger.addEventListener('click', close);
            });

            document.addEventListener('keydown', function (event) {
                if (event.key === 'Escape' && filters.classList.contains('is-open')) {
                    close();
                }
            });

            desktopQuery.addEventListener('change', function (event) {
                if (event.matches) {
                    close();
                }
            });
        })();
    </script>
    <script>
        (function () {
            const form = document.querySelector('[data-search-form]');

            if (!form) {
                return;
            }

            function digitsOnly(value) {
                return String(value || '').replace(/[^\d]/g, '');
            }

            function formatGroupedDigits(value) {
                return value.replace(/\B(?=(\d{3})+(?!\d))/g, ' ');
            }

            function caretPositionForDigits(value, digitsCount) {
                if (digitsCount <= 0) {
                    return 0;
                }

                let seenDigits = 0;

                for (let index = 0; index < value.length; index += 1) {
                    if (/\d/.test(value.charAt(index))) {
                        seenDigits += 1;
                    }

                    if (seenDigits >= digitsCount) {
                        return index + 1;
                    }
                }

                return value.length;
            }

            function formatGroupedInput(target) {
                if (!(target instanceof HTMLInputElement) || !target.hasAttribute('data-grouped-number')) {
                    return;
                }

                const selectionStart = typeof target.selectionStart === 'number' ? target.selectionStart : target.value.length;
                const digitsBeforeCaret = digitsOnly(target.value.slice(0, selectionStart)).length;
                const normalized = digitsOnly(target.value);
                const formatted = formatGroupedDigits(normalized);

                target.value = formatted;

                if (document.activeElement === target && typeof target.setSelectionRange === 'function') {
                    const nextCaretPosition = caretPositionForDigits(formatted, digitsBeforeCaret);
                    target.setSelectionRange(nextCaretPosition, nextCaretPosition);
                }
            }

            document.addEventListener('input', function (event) {
                const target = event.target;

                if (target instanceof HTMLElement && target.form === form) {
                    formatGroupedInput(target);
                }
            });

            document.addEventListener('change', function (event) {
                const target = event.target;

                if (target instanceof HTMLElement && target.hasAttribute('data-search-sort')) {
                    form.submit();
                }
            });
        })();
    </script>
    <script>
        (function () {
            const sidebar = document.querySelector('[data-search-filters]');

            if (!sidebar) {
                return;
            }

            const inner = sidebar.querySelector('.search-filter') || sidebar;
            const desktopQuery = window.matchMedia('(min-width: 1000px)');

            function readGap() {
                const styles = window.getComputedStyle(document.documentElement);
                const space4 = parseFloat(styles.getPropertyValue('--space-4')) || 24;
                const headerOffset = parseFloat(styles.getPropertyValue('--header-sticky-offset')) || 0;
                const head = document.querySelector('.search-page__head');
                const headHeight = head ? head.offsetHeight : 0;
                return { topGap: space4 + headerOffset + headHeight, bottomGap: space4 };
            }

            let currentTop = null;
            let lastScrollY = window.scrollY;
            let ticking = false;

            function reset() {
                sidebar.style.removeProperty('--sidebar-sticky-top');
                currentTop = null;
            }

            function update() {
                ticking = false;

                if (!desktopQuery.matches) {
                    if (currentTop !== null) {
                        reset();
                    }
                    lastScrollY = window.scrollY;
                    return;
                }

                const { topGap, bottomGap } = readGap();
                const sidebarHeight = inner.offsetHeight;
                const viewportHeight = window.innerHeight;
                const scrollY = window.scrollY;
                const delta = scrollY - lastScrollY;
                lastScrollY = scrollY;

                let nextTop;

                if (sidebarHeight + topGap <= viewportHeight) {
                    nextTop = topGap;
                } else {
                    const minTop = viewportHeight - sidebarHeight - bottomGap;
                    const maxTop = topGap;

                    if (currentTop === null) {
                        currentTop = topGap;
                    }

                    nextTop = currentTop - delta;
                    nextTop = Math.max(minTop, Math.min(maxTop, nextTop));
                }

                currentTop = nextTop;
                sidebar.style.setProperty('--sidebar-sticky-top', nextTop + 'px');
            }

            function onScroll() {
                if (ticking) {
                    return;
                }
                ticking = true;
                window.requestAnimationFrame(update);
            }

            window.addEventListener('scroll', onScroll, { passive: true });
            window.addEventListener('resize', function () {
                lastScrollY = window.scrollY;
                update();
            }, { passive: true });

            if ('ResizeObserver' in window) {
                new ResizeObserver(function () {
                    lastScrollY = window.scrollY;
                    update();
                }).observe(inner);
            }

            desktopQuery.addEventListener('change', function (event) {
                if (!event.matches) {
                    reset();
                } else {
                    lastScrollY = window.scrollY;
                    update();
                }
            });

            update();
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