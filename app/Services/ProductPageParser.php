<?php

namespace App\Services;

use DOMDocument;
use DOMElement;
use DOMXPath;
use Illuminate\Support\Str;

/**
 * Витягує назву, опис, ціну, валюту і фото товару з HTML-сторінки.
 *
 * Порядок джерел — від найточнішого до найзагальнішого:
 * JSON-LD schema.org/Product → мікророзмітка itemprop → Open Graph / Twitter → <title>.
 * Ціну беремо лише зі структурованих даних: краще порожнє поле, ніж випадкове число зі сторінки.
 */
class ProductPageParser
{
    private const DESCRIPTION_LIMIT = 500;

    private DOMXPath $xpath;

    /** @var array<string, string> */
    private array $meta = [];

    /** @var array<string, mixed>|null */
    private ?array $product = null;

    /**
     * @return array{title: ?string, description: ?string, price: ?float, currency: ?string, image_url: ?string}
     */
    public function parse(string $html, string $url, ?string $contentType = null): array
    {
        $this->load($this->toUtf8($html, $contentType));

        $offer = $this->offer();
        $baseUrl = $this->baseUrl($url);

        return [
            'title' => $this->title($url),
            'description' => $this->description(),
            'price' => $this->normalizePrice(
                $offer['price'] ?? $offer['lowPrice'] ?? $offer['priceSpecification']['price']
                    ?? $this->meta['product:price:amount'] ?? $this->meta['og:price:amount'] ?? $this->itemprop('price')
            ),
            'currency' => $this->normalizeCurrency(
                $offer['priceCurrency'] ?? $offer['priceSpecification']['priceCurrency']
                    ?? $this->meta['product:price:currency'] ?? $this->meta['og:price:currency'] ?? $this->itemprop('priceCurrency')
            ),
            'image_url' => $this->resolveUrl($this->image(), $baseUrl),
        ];
    }

    private function load(string $html): void
    {
        $dom = new DOMDocument;
        // Не-ASCII у числові сутності: так libxml не плутається з кодуванням сторінки
        $html = mb_encode_numericentity($html, [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
        @$dom->loadHTML($html, LIBXML_NONET | LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_COMPACT);

        $this->xpath = new DOMXPath($dom);
        $this->meta = [];
        $this->product = null;

        foreach ($this->xpath->query('//meta[@content]') as $node) {
            /** @var DOMElement $node */
            $key = strtolower($node->getAttribute('property') ?: $node->getAttribute('name') ?: $node->getAttribute('itemprop'));
            $content = trim($node->getAttribute('content'));

            if ($key !== '' && $content !== '' && ! isset($this->meta[$key])) {
                $this->meta[$key] = $content;
            }
        }

        foreach ($this->xpath->query('//script[@type="application/ld+json"]') as $script) {
            // Вміст <script> libxml не декодує — повертаємо не-ASCII, закодований у load()
            $json = mb_decode_numericentity(trim($script->textContent), [0x80, 0x10FFFF, 0, 0x1FFFFF], 'UTF-8');
            $data = json_decode($json, true);

            if (is_array($data) && $product = $this->findProduct($data)) {
                $this->product = $product;
                break;
            }
        }
    }

    /**
     * Пошук вузла Product у JSON-LD (масиви, @graph, ProductGroup).
     *
     * @return array<string, mixed>|null
     */
    private function findProduct(array $node): ?array
    {
        $types = array_map('strtolower', (array) ($node['@type'] ?? []));

        if (array_intersect($types, ['product', 'productgroup', 'individualproduct', 'productmodel'])) {
            return $node;
        }

        foreach (array_is_list($node) ? $node : ($node['@graph'] ?? []) as $child) {
            if (is_array($child) && $found = $this->findProduct($child)) {
                return $found;
            }
        }

        return null;
    }

    /**
     * @return array<string, mixed>
     */
    private function offer(): array
    {
        $offers = $this->product['offers'] ?? $this->product['hasVariant'][0]['offers'] ?? [];

        if (! is_array($offers)) {
            return [];
        }

        $offer = array_is_list($offers) ? ($offers[0] ?? []) : $offers;

        // AggregateOffer може містити вкладені offers
        if (! isset($offer['price']) && ! isset($offer['lowPrice']) && isset($offer['offers'])) {
            $nested = $offer['offers'];
            $offer = (array_is_list($nested) ? $nested[0] ?? [] : $nested) + $offer;
        }

        if (isset($offer['priceSpecification']) && array_is_list($offer['priceSpecification'])) {
            $offer['priceSpecification'] = $offer['priceSpecification'][0] ?? [];
        }

        return $offer;
    }

    private function title(string $url): ?string
    {
        $title = $this->text($this->product['name'] ?? null)
            ?? $this->meta['og:title'] ?? $this->meta['twitter:title'] ?? $this->itemprop('name')
            ?? $this->text($this->xpath->query('//title')->item(0)?->textContent);

        return $title ? $this->stripSiteName($title, $url) : null;
    }

    /**
     * «Навушники Sony — купити в інтернет-магазині Rozetka | Ціна» → «Навушники Sony»
     */
    private function stripSiteName(string $title, string $url): string
    {
        $parts = preg_split('/\s+[|–—•·:-]\s+/u', $title);
        $siteName = mb_strtolower($this->meta['og:site_name'] ?? '');
        $hostWord = mb_strtolower(Str::before(preg_replace('/^www\./', '', parse_url($url, PHP_URL_HOST) ?? ''), '.'));

        $isNoise = function (string $part) use ($siteName, $hostWord) {
            $part = mb_strtolower($part);

            return (mb_strlen($siteName) >= 3 && str_contains($part, $siteName))
                || (mb_strlen($hostWord) >= 4 && str_contains(str_replace(' ', '', $part), $hostWord))
                || preg_match('/^(купити|купить|ціна|цена|відгуки|отзывы|buy|price|shop|інтернет-магазин)/u', $part);
        };

        while (count($parts) > 1 && $isNoise(end($parts))) {
            array_pop($parts);
        }

        while (count($parts) > 1 && $isNoise($parts[0])) {
            array_shift($parts);
        }

        $total = count(preg_split('/\s+[|–—•·:-]\s+/u', $title));

        return count($parts) === $total ? $title : trim(implode(' - ', $parts));
    }

    private function description(): ?string
    {
        // Магазини часто пишуть опис для пошуковиків («низькі ціни, доставка…») — у вішлісті він лише заважає
        $candidates = [
            $this->text($this->product['description'] ?? null),
            $this->meta['og:description'] ?? $this->meta['twitter:description'] ?? $this->meta['description'] ?? null,
        ];

        foreach ($candidates as $candidate) {
            $description = $this->cleanDescription($candidate);

            if ($description && ! $this->looksLikeSeo($description)) {
                return $description;
            }
        }

        return null;
    }

    private function cleanDescription(?string $description): ?string
    {
        if (! $description) {
            return null;
        }

        $description = trim(preg_replace('/\s+/u', ' ', html_entity_decode(strip_tags($description), ENT_QUOTES | ENT_HTML5, 'UTF-8')));

        return Str::limit($description, self::DESCRIPTION_LIMIT, '…', preserveWords: true) ?: null;
    }

    private function looksLikeSeo(string $text): bool
    {
        if (preg_match('/[⭐✅👉🔥✔★☎]/u', $text)) {
            return true;
        }

        $markers = preg_match_all(
            '/купити|купить|низьк\w* цін|вигідн\w* цін|ціна|цена|гарант|доставк|кредит|оплат\w* частин|знижк|акці|інтернет-магазин|відгук|'
            .'buy now|free shipping|financing|trade in|best price|deals?\b|order online/iu',
            $text,
        );

        return $markers >= 2;
    }

    private function image(): ?string
    {
        $image = $this->product['image'] ?? null;

        if (is_array($image)) {
            $image = array_is_list($image) ? $image[0] ?? null : $image;
            $image = is_array($image) ? ($image['url'] ?? $image['contentUrl'] ?? null) : $image;
        }

        return $this->text($image)
            ?? $this->meta['og:image:secure_url'] ?? $this->meta['og:image'] ?? $this->meta['og:image:url']
            ?? $this->meta['twitter:image'] ?? $this->meta['twitter:image:src']
            ?? $this->attribute('//link[@rel="image_src"]', 'href')
            ?? $this->attribute('//*[@itemprop="image"]', 'content') ?? $this->attribute('//img[@itemprop="image"]', 'src');
    }

    private function itemprop(string $name): ?string
    {
        $node = $this->xpath->query('//*[@itemprop="'.$name.'"]')->item(0);

        if (! $node instanceof DOMElement) {
            return null;
        }

        return $this->text($node->getAttribute('content') ?: $node->textContent);
    }

    private function attribute(string $query, string $attribute): ?string
    {
        $node = $this->xpath->query($query)->item(0);

        return $node instanceof DOMElement ? $this->text($node->getAttribute($attribute)) : null;
    }

    private function text(mixed $value): ?string
    {
        if (! is_string($value) && ! is_numeric($value)) {
            return null;
        }

        $value = trim(preg_replace('/\s+/u', ' ', (string) $value));

        return $value === '' ? null : $value;
    }

    /**
     * «1 299,00» / «1.299,50» / «1,299.50» / «49.9» → float
     */
    public function normalizePrice(mixed $price): ?float
    {
        if (is_int($price) || is_float($price)) {
            return $price > 0 ? round((float) $price, 2) : null;
        }

        if (! is_string($price)) {
            return null;
        }

        $price = preg_replace('/[^\d.,]/u', '', $price);

        if ($price === '' || ! preg_match('/\d/', $price)) {
            return null;
        }

        $lastComma = strrpos($price, ',');
        $lastDot = strrpos($price, '.');

        if ($lastComma !== false && $lastDot !== false) {
            $decimal = $lastComma > $lastDot ? ',' : '.';
        } elseif ($lastComma !== false) {
            // «1,299» — тисячі, «49,90» — копійки
            $decimal = preg_match('/^\d{1,3}(,\d{3})+$/', $price) ? null : ',';
        } elseif ($lastDot !== false) {
            $decimal = preg_match('/^\d{1,3}(\.\d{3}){2,}$/', $price) ? null : '.';
        } else {
            $decimal = null;
        }

        $thousands = $decimal === ',' ? '.' : ($decimal === '.' ? ',' : '.,');
        $normalized = str_replace(str_split($thousands), '', $price);
        $normalized = $decimal === ',' ? str_replace(',', '.', $normalized) : $normalized;
        $value = (float) $normalized;

        return $value > 0 ? round($value, 2) : null;
    }

    public function normalizeCurrency(mixed $currency): ?string
    {
        if (! is_string($currency) || trim($currency) === '') {
            return null;
        }

        $currency = mb_strtolower(trim($currency));

        return match (true) {
            in_array($currency, ['uah', '₴', 'грн', 'грн.'], true) => 'UAH',
            in_array($currency, ['usd', '$', 'us$'], true) => 'USD',
            in_array($currency, ['eur', '€'], true) => 'EUR',
            (bool) preg_match('/^[a-z]{3}$/', $currency) => strtoupper($currency),
            default => null,
        };
    }

    private function baseUrl(string $url): string
    {
        $base = $this->attribute('//base[@href]', 'href');

        return $base ? ($this->resolveUrl($base, $url) ?? $url) : $url;
    }

    /**
     * Абсолютне посилання з відносного (лише http/https).
     */
    public function resolveUrl(?string $relative, string $base): ?string
    {
        if (! $relative || str_starts_with($relative, 'data:')) {
            return null;
        }

        $relative = html_entity_decode($relative, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $baseParts = parse_url($base);
        $scheme = $baseParts['scheme'] ?? 'https';
        $origin = $scheme.'://'.($baseParts['host'] ?? '').(isset($baseParts['port']) ? ':'.$baseParts['port'] : '');

        $absolute = match (true) {
            (bool) preg_match('#^https?://#i', $relative) => $relative,
            str_starts_with($relative, '//') => $scheme.':'.$relative,
            str_starts_with($relative, '/') => $origin.$relative,
            default => $origin.$this->removeDotSegments(
                preg_replace('#/[^/]*$#', '/', $baseParts['path'] ?? '/').$relative
            ),
        };

        return filter_var($absolute, FILTER_VALIDATE_URL) ? $absolute : null;
    }

    private function removeDotSegments(string $path): string
    {
        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '..') {
                array_pop($segments);
            } elseif ($segment !== '.') {
                $segments[] = $segment;
            }
        }

        return '/'.ltrim(implode('/', $segments), '/');
    }

    /**
     * Сторінки в windows-1251 тощо перекодовуємо в UTF-8.
     */
    private function toUtf8(string $html, ?string $contentType): string
    {
        $charset = null;

        if ($contentType && preg_match('/charset=["\']?([\w-]+)/i', $contentType, $match)) {
            $charset = $match[1];
        } elseif (preg_match('/<meta[^>]+charset=["\']?([\w-]+)/i', substr($html, 0, 4096), $match)) {
            $charset = $match[1];
        }

        if ($charset && ! in_array(strtolower($charset), ['utf-8', 'utf8'], true)) {
            try {
                return mb_convert_encoding($html, 'UTF-8', $charset);
            } catch (\ValueError) {
                // невідоме кодування — пробуємо як є
            }
        }

        return mb_check_encoding($html, 'UTF-8') ? $html : mb_convert_encoding($html, 'UTF-8', 'Windows-1251');
    }
}
