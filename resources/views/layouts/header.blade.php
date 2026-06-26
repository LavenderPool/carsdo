@php
    $currentPath = trim(request()->decodedPath(), '/');
    $route = request()->route();
    $routeBrand = $route?->parameter('brand');
    $currentBrandSlug = null;
    $currentBrandName = null;

    if ($routeBrand instanceof \App\Models\Brand) {
        $currentBrandSlug = $routeBrand->slug;
        $currentBrandName = $routeBrand->name;
    } elseif (is_string($routeBrand) && $routeBrand !== '') {
        $currentBrandSlug = $routeBrand;
    }

    $routeBrandSlug = $route?->parameter('brand_slug');

    if ($currentBrandSlug === null && is_string($routeBrandSlug) && $routeBrandSlug !== '') {
        $currentBrandSlug = $routeBrandSlug;
    }

    $isActive = static function (array|string $patterns) use ($currentPath): bool {
        foreach ((array) $patterns as $pattern) {
            if ($pattern === '') {
                if ($currentPath === '') {
                    return true;
                }

                continue;
            }

            if (\Illuminate\Support\Str::is($pattern, $currentPath)) {
                return true;
            }
        }

        return false;
    };

    $headerBrandsList = collect($headerPopularBrands ?? []);
    $headerSearchQuery = trim((string) request()->query('q', ''));
    $allBrandsHref = match (true) {
        $currentPath === '' => '/brands/',
        $currentPath === 'brands' => '/brands/',
        default => '#minsk',
    };

    if ($currentBrandSlug !== null) {
        $currentBrand = $headerBrandsList->firstWhere('slug', $currentBrandSlug);

        if ($currentBrand === null && $routeBrand instanceof \App\Models\Brand) {
            $currentBrand = $routeBrand;
        }

        if ($currentBrand === null && $currentBrandName !== null) {
            $currentBrand = (object) [
                'name' => $currentBrandName,
                'slug' => $currentBrandSlug,
            ];
        }

        if ($currentBrand !== null) {
            $headerBrandsList = $headerBrandsList
                ->reject(static fn ($brand) => data_get($brand, 'slug') === $currentBrandSlug)
                ->prepend($currentBrand)
                ->take(15)
                ->values();
        }
    }
@endphp

<section class="site-header" data-header>
    <div class="start">
        <div class="header-bar">
            <div class="logo">
                <a href="/" aria-label="CarsDo">
                    <span class="logo-mark">
                        <span class="logo-mark-part">Cars</span>Do</span>
                </a>
            </div>

            <div class="header-actions">
                <form
                    class="site-search"
                    action="{{ route('search') }}"
                    method="get"
                    role="search"
                    aria-label="Поиск по сайту"
                    data-site-search
                    novalidate
                >
                    <div class="site-search__field">
                        <span class="site-search__icon" aria-hidden="true">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="lucide lucide-search-icon lucide-search">
                                <path d="m21 21-4.34-4.34" />
                                <circle cx="11" cy="11" r="8" />
                            </svg>
                        </span>
                        <label class="sr-only" for="site-header-search">Поиск по сайту</label>
                        <input
                            id="site-header-search"
                            name="q"
                            type="search"
                            value="{{ $headerSearchQuery }}"
                            placeholder="Поиск по моделям и брендам"
                            autocomplete="off"
                            inputmode="search"
                            aria-autocomplete="list"
                            aria-expanded="false"
                            aria-controls="site-header-search-panel"
                            data-search-input
                        >
                        <button class="site-search__submit" type="submit">Найти</button>
                    </div>
                    <div class="site-search__panel" id="site-header-search-panel" hidden data-search-panel>
                        <div class="site-search__status" data-search-status>Введите минимум 2 символа.</div>
                        <div class="site-search__sections" data-search-sections hidden>
                            <section class="site-search__section" data-search-section="brands" hidden>
                                <div class="site-search__section-title">Бренды</div>
                                <ul class="site-search__list" data-search-list="brands" role="listbox"></ul>
                            </section>
                            <section class="site-search__section" data-search-section="models" hidden>
                                <div class="site-search__section-title">Модели</div>
                                <ul class="site-search__list" data-search-list="models" role="listbox"></ul>
                            </section>
                        </div>
                    </div>
                </form>

                <button class="header-burger" type="button" aria-expanded="false" aria-controls="site-header-nav">
                    <span class="header-burger__lines" aria-hidden="true">
                        <span></span>
                        <span></span>
                        <span></span>
                    </span>
                    <span class="header-burger__label">Меню</span>
                </button>
            </div>
        </div>
    </div>

    <div class="header-panel" id="site-header-nav">
        <div class="menu">
            <ul class="main_menu">
                <li><a href="/" @class(['is-active' => $isActive('')])>Автомобили</a></li>
                <li><a href="/new-cars-{{ $catalogYear }}/" @class(['is-active' => $isActive("new-cars-$catalogYear")])>{{ $catalogYear }}</a></li>
                <li><a href="/new-cars-{{ $catalogPrevYear }}/" @class(['is-active' => $isActive("new-cars-$catalogPrevYear")])>{{ $catalogPrevYear }}</a></li>
                <li><a href="/cars-photo/" @class(['is-active' => $isActive(['cars-photo', 'cars-photo/*', '*/photo'])])>Фото</a></li>
                <li><a href="/test-drive/" @class(['is-active' => $isActive(['test-drive', 'test-drive/*', '*/test-drive'])])>Тест-драйвы</a></li>
                <li><a href="/crash-test/" @class(['is-active' => $isActive(['crash-test', 'crash-test/*', '*/crash-test'])])>Краш-тесты</a></li>
                <li><a href="/electric-cars/" @class(['is-active' => $isActive(['electric-cars', 'electric-cars/*', '*/electric-cars'])])>Электромобили</a></li>
                <li><a href="/blog/" @class(['is-active' => $isActive(['blog', 'blog/*'])])>Блог</a></li>
            </ul>
        </div>

        <div class="brands-menu" id="carsnavi">
            <ul>
                @foreach ($headerBrandsList as $brand)
                    @php
                        $brandSlug = data_get($brand, 'slug');
                        $brandName = data_get($brand, 'name');
                    @endphp
                    @if (filled($brandSlug) && filled($brandName))
                        <li>
                            <a href="/{{ $brandSlug }}/" @class(['is-active' => $currentBrandSlug === $brandSlug])>{{ $brandName }}</a>
                        </li>
                    @endif
                @endforeach
                <li><a href="{{ $allBrandsHref }}" @class(['is-active' => $isActive('brands')])>Все марки</a></li>
            </ul>
        </div>
    </div>
</section>

<script>
    (function () {
        const header = document.querySelector('[data-header]');
        const searchSuggestUrl = @json(route('search.suggest'));
        const minSearchQueryLength = 2;

        if (!header) {
            return;
        }

        const burger = header.querySelector('.header-burger');
        const searchForm = header.querySelector('[data-site-search]');
        const searchInput = searchForm ? searchForm.querySelector('[data-search-input]') : null;
        const searchPanel = searchForm ? searchForm.querySelector('[data-search-panel]') : null;
        const searchStatus = searchForm ? searchForm.querySelector('[data-search-status]') : null;
        const searchSections = searchForm ? searchForm.querySelector('[data-search-sections]') : null;
        const brandSection = searchForm ? searchForm.querySelector('[data-search-section="brands"]') : null;
        const modelSection = searchForm ? searchForm.querySelector('[data-search-section="models"]') : null;
        const brandList = searchForm ? searchForm.querySelector('[data-search-list="brands"]') : null;
        const modelList = searchForm ? searchForm.querySelector('[data-search-list="models"]') : null;
        const statusMessages = {
            minQuery: 'Введите минимум 2 символа.',
            loading: 'Ищем совпадения...',
            empty: 'Ничего не найдено. Нажмите Enter, чтобы открыть страницу результатов.',
        };
        let searchDebounceTimer = null;
        let activeOptionIndex = -1;
        let activeSearchController = null;
        let latestSearchToken = 0;

        const escapeHtml = function (value) {
            return String(value)
                .replaceAll('&', '&amp;')
                .replaceAll('<', '&lt;')
                .replaceAll('>', '&gt;')
                .replaceAll('"', '&quot;')
                .replaceAll("'", '&#039;');
        };

        const getSearchOptions = function () {
            if (!searchPanel) {
                return [];
            }

            return Array.from(searchPanel.querySelectorAll('[data-search-option]'));
        };

        const cancelActiveSearchRequest = function () {
            window.clearTimeout(searchDebounceTimer);
            latestSearchToken += 1;

            if (activeSearchController) {
                activeSearchController.abort();
                activeSearchController = null;
            }
        };

        const resetActiveOption = function () {
            activeOptionIndex = -1;
            getSearchOptions().forEach(function (option) {
                option.classList.remove('site-search__option--active');
                option.removeAttribute('aria-selected');
            });
        };

        const setSearchPanelOpen = function (isOpen) {
            if (!searchForm || !searchPanel || !searchInput) {
                return;
            }

            searchForm.classList.toggle('is-open', isOpen);
            searchPanel.hidden = !isOpen;
            searchInput.setAttribute('aria-expanded', isOpen ? 'true' : 'false');

            if (!isOpen) {
                resetActiveOption();
            }
        };

        const updateActiveOption = function (nextIndex) {
            const options = getSearchOptions();

            if (options.length === 0) {
                resetActiveOption();

                return;
            }

            if (nextIndex < 0) {
                nextIndex = options.length - 1;
            }

            if (nextIndex >= options.length) {
                nextIndex = 0;
            }

            options.forEach(function (option, index) {
                const isActive = index === nextIndex;

                option.classList.toggle('site-search__option--active', isActive);

                if (isActive) {
                    option.setAttribute('aria-selected', 'true');
                    option.scrollIntoView({
                        block: 'nearest',
                    });
                } else {
                    option.removeAttribute('aria-selected');
                }
            });

            activeOptionIndex = nextIndex;
        };

        const setSearchStatus = function (message) {
            if (!searchStatus || !searchSections) {
                return;
            }

            searchStatus.hidden = false;
            searchStatus.textContent = message;
            searchSections.hidden = true;
            setSearchPanelOpen(true);
        };

        const renderSearchList = function (list, items, type) {
            if (!list) {
                return;
            }

            list.innerHTML = items.map(function (item) {
                const title = escapeHtml(item.name ?? '');
                const href = escapeHtml(item.url ?? '#');
                const meta = type === 'brands'
                    ? 'Марка'
                    : [item.brand_name, item.year].filter(Boolean).map(escapeHtml).join(' · ');

                return '<li class="site-search__item">' +
                    '<a class="site-search__option" href="' + href + '" role="option" data-search-option>' +
                    '<span class="site-search__option-title">' + title + '</span>' +
                    '<span class="site-search__option-meta">' + meta + '</span>' +
                    '</a>' +
                    '</li>';
            }).join('');
        };

        const renderSearchResults = function (payload) {
            if (!searchSections || !brandSection || !modelSection || !searchStatus) {
                return;
            }

            const brands = Array.isArray(payload.brands) ? payload.brands : [];
            const models = Array.isArray(payload.models) ? payload.models : [];

            renderSearchList(brandList, brands, 'brands');
            renderSearchList(modelList, models, 'models');

            brandSection.hidden = brands.length === 0;
            modelSection.hidden = models.length === 0;
            searchSections.classList.toggle('site-search__sections--single', brands.length === 0 || models.length === 0);
            resetActiveOption();

            if (brands.length === 0 && models.length === 0) {
                setSearchStatus(statusMessages.empty);

                return;
            }

            searchStatus.hidden = true;
            searchSections.hidden = false;
            setSearchPanelOpen(true);
        };

        const fetchSearchSuggestions = function (query) {
            if (!searchInput) {
                return;
            }

            cancelActiveSearchRequest();

            activeSearchController = new AbortController();

            const currentToken = latestSearchToken;
            const requestUrl = new URL(searchSuggestUrl, window.location.origin);

            requestUrl.searchParams.set('q', query);
            setSearchStatus(statusMessages.loading);

            window.fetch(requestUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: activeSearchController.signal,
            })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('Search request failed');
                    }

                    return response.json();
                })
                .then(function (payload) {
                    if (currentToken !== latestSearchToken) {
                        return;
                    }

                    renderSearchResults(payload);
                })
                .catch(function (error) {
                    if (error.name === 'AbortError') {
                        return;
                    }

                    setSearchStatus('Не удалось загрузить подсказки. Нажмите Enter для полного поиска.');
                });
        };

        const handleSearchValue = function (rawValue) {
            const query = rawValue.trim();

            if (query.length === 0) {
                cancelActiveSearchRequest();
                setSearchPanelOpen(false);

                return;
            }

            if (query.length < minSearchQueryLength) {
                cancelActiveSearchRequest();
                setSearchStatus(statusMessages.minQuery);

                return;
            }

            searchDebounceTimer = window.setTimeout(function () {
                fetchSearchSuggestions(query);
            }, 180);
        };

        if (searchForm && searchInput) {
            searchInput.addEventListener('input', function (event) {
                handleSearchValue(event.target.value);
            });

            searchInput.addEventListener('focus', function () {
                if (searchInput.value.trim() !== '') {
                    handleSearchValue(searchInput.value);
                }
            });

            searchInput.addEventListener('keydown', function (event) {
                if (event.key === 'Escape') {
                    setSearchPanelOpen(false);

                    return;
                }

                if (event.key !== 'ArrowDown' && event.key !== 'ArrowUp' && event.key !== 'Enter') {
                    return;
                }

                const options = getSearchOptions();

                if ((event.key === 'ArrowDown' || event.key === 'ArrowUp') && options.length > 0) {
                    event.preventDefault();
                    updateActiveOption(activeOptionIndex + (event.key === 'ArrowDown' ? 1 : -1));

                    return;
                }

                if (event.key === 'Enter' && !searchPanel?.hidden && activeOptionIndex >= 0) {
                    const activeOption = options[activeOptionIndex];

                    if (activeOption) {
                        event.preventDefault();
                        window.location.href = activeOption.href;
                    }
                }
            });

            document.addEventListener('click', function (event) {
                if (!searchForm.contains(event.target)) {
                    setSearchPanelOpen(false);
                }
            });
        }

        if (!burger) {
            return;
        }

        const syncState = function (isOpen) {
            header.classList.toggle('is-open', isOpen);
            burger.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        };

        burger.addEventListener('click', function () {
            syncState(!header.classList.contains('is-open'));
        });

        header.querySelectorAll('.main_menu a, #carsnavi a').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 900) {
                    syncState(false);
                }
            });
        });

        document.addEventListener('keydown', function (event) {
            if (event.key === 'Escape') {
                syncState(false);
            }
        });

        window.addEventListener('resize', function () {
            if (window.innerWidth >= 900) {
                syncState(false);
            }
        });
    })();
</script>