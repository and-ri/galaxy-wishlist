<?php

use App\Models\User;
use App\Models\Wish;
use App\Services\ProductPageParser;
use App\Services\RemoteFetcher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Client\Request as ClientRequest;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Без справжнього DNS: усі хости «публічні», крім явно внутрішніх
    $this->app->instance(RemoteFetcher::class, new RemoteFetcher(
        fn (string $host) => $host === 'intranet.local' ? ['10.0.0.5'] : ['93.184.216.34'],
    ));
    $this->user = User::factory()->create();
});

function parsePage(string $html, string $url = 'https://shop.example.com/catalog/item/42'): array
{
    return (new ProductPageParser)->parse($html, $url);
}

test('бере дані з JSON-LD Product, навіть у @graph і з масивом offers', function () {
    $data = parsePage(<<<'HTML'
        <html><head>
        <title>Навушники Sony WH-1000XM5 — купити в інтернет-магазині | Shop</title>
        <script type="application/ld+json">
        {"@context":"https://schema.org","@graph":[
            {"@type":"BreadcrumbList","itemListElement":[]},
            {"@type":["Product"],"name":"Навушники Sony WH-1000XM5","description":"<p>Бездротові   навушники</p>",
             "image":[{"@type":"ImageObject","url":"/img/sony.jpg"}],
             "offers":[{"@type":"Offer","price":"13999.00","priceCurrency":"UAH"}]}
        ]}
        </script></head><body>Розмір 1920px, 2024 рік</body></html>
        HTML);

    expect($data)->toBe([
        'title' => 'Навушники Sony WH-1000XM5',
        'description' => 'Бездротові навушники',
        'price' => 13999.0,
        'currency' => 'UAH',
        'image_url' => 'https://shop.example.com/img/sony.jpg',
    ]);
});

test('читає Open Graph з будь-яким порядком атрибутів і лапками', function () {
    $data = parsePage(<<<'HTML'
        <html><head>
        <meta content='Кавомашина De&#039;Longhi | Rozetka' property='og:title'>
        <meta content="Rozetka" property="og:site_name">
        <meta data-x="1" content="//cdn.example.com/a.jpg" property="og:image" />
        <meta content="8 499,00" property="product:price:amount">
        <meta content="грн" property="product:price:currency">
        </head></html>
        HTML);

    expect($data['title'])->toBe("Кавомашина De'Longhi")
        ->and($data['price'])->toBe(8499.0)
        ->and($data['currency'])->toBe('UAH')
        ->and($data['image_url'])->toBe('https://cdn.example.com/a.jpg');
});

test('відкидає SEO-опис магазину, але залишає звичайний', function () {
    $seo = parsePage('<meta property="og:description" content="Мобільний телефон Apple iPhone 15. Низькі ціни, гарантія, знижки, кредит в інтернет-магазині ROZETKA">');
    $plain = parsePage('<meta property="og:description" content="Бездротові навушники з шумозаглушенням, 30 годин роботи.">');

    expect($seo['description'])->toBeNull()
        ->and($plain['description'])->toBe('Бездротові навушники з шумозаглушенням, 30 годин роботи.');
});

test('не вигадує ціну і валюту, якщо їх немає в розмітці', function () {
    $data = parsePage('<html><head><title>Блог</title><script>$(".x").css("width", "1280px")</script></head><body>Ціна 2024</body></html>');

    expect($data['price'])->toBeNull()->and($data['currency'])->toBeNull();
});

test('бере ціну з мікророзмітки itemprop', function () {
    $data = parsePage('<div itemscope itemtype="https://schema.org/Product"><h1 itemprop="name">LEGO Бонсай</h1><span itemprop="price" content="49.90">49,90 €</span><meta itemprop="priceCurrency" content="EUR"></div>');

    expect($data['title'])->toBe('LEGO Бонсай')->and($data['price'])->toBe(49.9)->and($data['currency'])->toBe('EUR');
});

test('нормалізує різні формати ціни', function (string $raw, ?float $expected) {
    expect((new ProductPageParser)->normalizePrice($raw))->toBe($expected);
})->with([
    ['13999', 13999.0],
    ['1 299,00 ₴', 1299.0],
    ['1.299,50', 1299.5],
    ['1,299.50', 1299.5],
    ['1,299', 1299.0],
    ['49,90', 49.9],
    ['49.9', 49.9],
    ['0', null],
    ['безкоштовно', null],
]);

test('перекодовує сторінки у windows-1251', function () {
    $html = mb_convert_encoding('<html><head><meta charset="windows-1251"><title>Чайник Bosch</title></head></html>', 'Windows-1251', 'UTF-8');

    expect(parsePage($html)['title'])->toBe('Чайник Bosch');
});

test('розв\'язує відносний шлях до фото відносно сторінки', function () {
    $data = parsePage('<meta property="og:image" content="../images/photo.png">', 'https://shop.example.com/catalog/item/42');

    expect($data['image_url'])->toBe('https://shop.example.com/catalog/images/photo.png');
});

test('API повертає розібрані дані', function () {
    Http::fake(['shop.example.com/*' => Http::response(
        '<meta property="og:title" content="Kindle"><meta property="product:price:amount" content="159"><meta property="product:price:currency" content="USD">',
        200,
        ['Content-Type' => 'text/html; charset=utf-8'],
    )]);

    $this->actingAs($this->user)->postJson('/api/parse-url', ['url' => 'https://shop.example.com/kindle'])
        ->assertOk()
        ->assertJson(['success' => true, 'data' => ['title' => 'Kindle', 'price' => 159, 'currency' => 'USD']]);
});

test('не ходить на внутрішні адреси (SSRF)', function (string $url) {
    Http::fake();

    $this->actingAs($this->user)->postJson('/api/parse-url', ['url' => $url])
        ->assertStatus(422)
        ->assertJson(['success' => false]);

    Http::assertNothingSent();
})->with([
    'localhost' => 'http://127.0.0.1/admin',
    'метадані хмари' => 'http://169.254.169.254/latest/meta-data',
    'приватна мережа' => 'http://192.168.1.1/',
    'IPv6 loopback' => 'http://[::1]/',
    'хост з внутрішньою IP' => 'http://intranet.local/',
    'нестандартний порт' => 'https://shop.example.com:8080/',
]);

test('зберігає лише справжні зображення і з розширенням за вмістом', function () {
    Storage::fake('public');
    $png = base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNk+M9QDwADhgGAWjR9awAAAABJRU5ErkJggg==');

    Http::fake([
        'cdn.example.com/shell.php' => Http::response('<?php system($_GET["c"]);', 200, ['Content-Type' => 'image/png']),
        'cdn.example.com/photo.php' => Http::response($png, 200),
    ]);

    $this->actingAs($this->user)->postJson('/api/download-image', ['image_url' => 'https://cdn.example.com/shell.php'])
        ->assertStatus(422);

    $path = $this->actingAs($this->user)->postJson('/api/download-image', ['image_url' => 'https://cdn.example.com/photo.php'])
        ->assertOk()
        ->json('path');

    expect($path)->toEndWith('.png');
    expect(Storage::disk('public')->allFiles())->toBe([$path]);
});

test('приймає image_path лише від автозаповнення', function () {
    $wish = Wish::create(['user_id' => $this->user->id, 'title' => 'Книга']);

    $this->actingAs($this->user)->put(route('wishes.update', $wish), [
        'title' => 'Книга',
        'image_path' => 'avatars/someone-else.jpg',
    ])->assertSessionHasErrors('image_path');

    expect($wish->fresh()->image)->toBeNull();
});

test('сторінки окремого бажання немає', function () {
    $wish = Wish::create(['user_id' => $this->user->id, 'title' => 'Книга']);

    $this->actingAs($this->user)->get("/wishes/{$wish->id}")->assertStatus(405);
});

test('надсилає браузерні заголовки', function () {
    Http::fake(['*' => Http::response('<title>X</title>', 200, ['Content-Type' => 'text/html'])]);

    $this->actingAs($this->user)->postJson('/api/parse-url', ['url' => 'https://shop.example.com/x']);

    Http::assertSent(fn (ClientRequest $request) => $request->hasHeader('Accept-Language') && str_contains($request->header('User-Agent')[0], 'Chrome/131'));
});
