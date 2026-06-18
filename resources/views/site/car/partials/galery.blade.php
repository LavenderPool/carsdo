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
    $galleryThumbsClass = isset($galleryThumbsClass) && is_string($galleryThumbsClass) && $galleryThumbsClass !== ''
        ? $galleryThumbsClass
        : 'preview_photo_mini';

    $galleryPhotos = isset($photos) && $photos instanceof \Illuminate\Support\Collection
        ? $photos
        : $car->photos
            ->concat($car->photoGroups->flatMap->photos)
            ->filter(fn ($photo) => filled($photo->photo_path))
            ->unique(fn ($photo) => $photo->id)
            ->values();

    $galleryMainPhoto = $galleryPhotos->first()?->url() ?: $car->coverUrl();
@endphp

@if ($galleryPhotos->isNotEmpty())
    <div id="{{ $galleryBlockId }}">
        <div style="width: 100%; margin:10px 0;"></div>
        <div><img class="{{ $galleryImageClass }}" src="{{ $galleryMainPhoto }}"></div>
        <div class="{{ $galleryThumbsClass }}">
            @foreach ($galleryPhotos->take(6) as $photo)
                <img src="{{ $photo->url() }}">
            @endforeach
        </div>
        <div class="dop_photo"><a href="{{ $carPath }}/photo/">ВСЕ ФОТО</a></div>
        <div>
            <script type="text/javascript">
                $('.{{ $galleryThumbsClass }}').delegate('img', 'click', function () {
                    $('.{{ $galleryImageClass }}').attr('src', $(this).attr('src').replace('thumb', 'large'));
                });
            </script>
        </div>
    </div>
@endif