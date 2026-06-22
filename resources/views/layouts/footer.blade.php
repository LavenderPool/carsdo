<section>
    @unless ($hideFooterBrands ?? false)
        <div id="minsk">
            <div class="menu-bottom-text">Новые <a href="/new-cars-{{ $catalogYear }}/">автомобили {{ $catalogYear }}</a> и будущие новинки автопрома в России</div>

            <div class="menu-bottom">
                <ul>
                    <li><a style="font-size:0.8em;" href="/electric-cars/">Электромобили</a></li>
                    @foreach ($footerBrandsActive as $brand)
                        <li><a href="/{{ $brand->slug }}/">{{ $brand->name }}</a></li>
                    @endforeach
                </ul>
            </div>

            <div class="menu-bottom-text">Марки авто покинувшие российский рынок</div>

            <div class="menu-bottom">
                <ul>
                    @foreach ($footerBrandsLeft as $brand)
                        <li><a href="/{{ $brand->slug }}/">{{ $brand->name }}</a></li>
                    @endforeach
                </ul>
            </div>
        </div>
    @endunless
    <footer>
        <div id="athens">
            <div id="end">
                <table class="endos">
                    <tbody>
                        <tr>
                            <td style="font-family:arial;">© CarsDo.ru | КарсДо.ру - <a href="/">Новые автомобили в
                                    России</a></td>
                        </tr>
                        <tr>
                            <td><a href="/sitemap.xml">Sitemap XML</a></td>
                        </tr>

                        <tr>
                            <td colspan="2">
                                <div class="eww">Вся представленная на сайте информация носит информационный характер и
                                    ни при каких условиях не является публичной офертой (Статья 437 Гражданского кодекса
                                    России). Цены на сайте могут отличаться от действительных у официальных дилеров и
                                    других автосалонов.</div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </footer>
</section>