@php
    $demoKworkUrl = 'https://kwork.ru/user/sergeyalekseevi4';
    $demoTelegramUrl = 'https://t.me/LavandaDev';
@endphp

<div class="demo-floating-label" aria-label="Демонстрационная информация">
    <div class="demo-floating-label__title">Демонстрационная версия</div>
    <div class="demo-floating-label__links">
        <a href="{{ $demoKworkUrl }}" target="_blank" rel="noopener noreferrer">Kwork</a>
        <span class="demo-floating-label__separator">|</span>
        <a href="{{ $demoTelegramUrl }}" target="_blank" rel="noopener noreferrer">Telegram</a>
    </div>
</div>
