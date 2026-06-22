@php
    $carPath = isset($carPath) && is_string($carPath) && $carPath !== ''
        ? $carPath
        : '/'.$brand->slug.'/'.$car->slug;
    $galleryBlockId = isset($galleryBlockId) && is_string($galleryBlockId) && $galleryBlockId !== ''
        ? $galleryBlockId
        : 'block_price4';
    $galleryImageClass = isset($galleryImageClass) && is_string($galleryImageClass) && $galleryImageClass !== ''
        ? $galleryImageClass
        : 'preview_photo';
    $galleryPhotos = isset($photos) && $photos instanceof \Illuminate\Support\Collection
        ? $photos
        : $car->photos
            ->concat($car->photoGroups->flatMap->photos)
            ->filter(fn ($photo) => filled($photo->photo_path))
            ->unique(fn ($photo) => $photo->id)
            ->values();

    $gallerySliderPhotos = $galleryPhotos->take(6)->values();
@endphp

@if ($gallerySliderPhotos->isNotEmpty())
    <div id="{{ $galleryBlockId }}">
        <div style="width: 100%; margin:10px 0;"></div>
        <div class="car-gallery-main-photo">
            <div class="car-gallery-slider" data-gallery-slider tabindex="0">
                <button class="car-gallery-slider__control car-gallery-slider__control_prev" type="button" data-gallery-prev aria-label="Предыдущее фото">&#10094;</button>
                <div class="car-gallery-slider__viewport">
                    <div class="car-gallery-slider__track" data-gallery-track>
                        @foreach ($gallerySliderPhotos as $index => $photo)
                            <div class="car-gallery-slider__slide" data-gallery-slide data-gallery-index="{{ $index }}">
                                <img class="{{ $galleryImageClass }}" src="{{ $photo->url() }}" data-car-image="true">
                            </div>
                        @endforeach
                    </div>
                    <a class="car-gallery-slider__cta" href="{{ $carPath }}/photo/">Перейти в галерею</a>
                </div>
                <button class="car-gallery-slider__control car-gallery-slider__control_next" type="button" data-gallery-next aria-label="Следующее фото">&#10095;</button>
            </div>
        </div>
        <div>
            <script type="text/javascript">
                (function () {
                    const galleryRoot = document.getElementById('{{ $galleryBlockId }}');
                    if (!galleryRoot) return;

                    const slider = galleryRoot.querySelector('[data-gallery-slider]');
                    const prevControl = galleryRoot.querySelector('[data-gallery-prev]');
                    const nextControl = galleryRoot.querySelector('[data-gallery-next]');
                    const slides = Array.from(galleryRoot.querySelectorAll('[data-gallery-slide]'));
                    if (!slider || !prevControl || !nextControl || !slides.length) return;

                    const slidesCount = slides.length;
                    if (!slidesCount) return;

                    let currentIndex = 0;
                    slider.classList.toggle('is-single', slidesCount === 1);

                    function getOffset(index) {
                        let offset = index - currentIndex;

                        if (offset > slidesCount / 2) {
                            offset -= slidesCount;
                        }

                        if (offset < -slidesCount / 2) {
                            offset += slidesCount;
                        }

                        return offset;
                    }

                    function goTo(index) {
                        currentIndex = ((index % slidesCount) + slidesCount) % slidesCount;

                        slides.forEach(function (slide, slideIndex) {
                            const offset = getOffset(slideIndex);
                            const position =
                                offset === 0
                                    ? 'active'
                                    : offset === -1
                                        ? 'prev'
                                        : offset === 1
                                            ? 'next'
                                            : offset < 0
                                                ? 'hidden-left'
                                                : 'hidden-right';

                            slide.dataset.position = position;
                            slide.style.setProperty('--gallery-offset', String(offset));
                            slide.setAttribute('aria-hidden', offset === 0 ? 'false' : 'true');
                        });
                    }

                    slides.forEach(function (slide) {
                        slide.addEventListener('click', function () {
                            const nextIndex = Number(slide.getAttribute('data-gallery-index'));
                            if (Number.isNaN(nextIndex) || nextIndex === currentIndex) return;
                            goTo(nextIndex);
                        });
                    });

                    prevControl.addEventListener('click', function () {
                        goTo(currentIndex - 1);
                    });

                    nextControl.addEventListener('click', function () {
                        goTo(currentIndex + 1);
                    });

                    slider.addEventListener('keydown', function (event) {
                        if (event.key === 'ArrowLeft') {
                            event.preventDefault();
                            goTo(currentIndex - 1);
                        }

                        if (event.key === 'ArrowRight') {
                            event.preventDefault();
                            goTo(currentIndex + 1);
                        }
                    });

                    goTo(0);
                })();
            </script>
        </div>
    </div>
@endif