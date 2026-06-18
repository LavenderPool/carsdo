<?php

namespace App\Support\Seo;

use RalphJSmit\Laravel\SEO\Support\SEOData;

final readonly class ResolvedPageSeo
{
    public function __construct(
        public SEOData $seoData,
        public ?string $h1 = null,
    ) {}
}
