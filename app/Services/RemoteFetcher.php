<?php

namespace App\Services;

use Closure;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Psr\Http\Message\UriInterface;

/**
 * Завантаження сторінок і зображень за посиланням від користувача.
 *
 * Захищає від SSRF: дозволені лише http(s) на стандартних портах і лише
 * публічні IP-адреси — як для першого запиту, так і для кожного редиректу.
 */
class RemoteFetcher
{
    private const USER_AGENT = 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/131.0.0.0 Safari/537.36';

    /**
     * @param  (Closure(string): list<string>)|null  $resolver  DNS-резолвер (підміняється в тестах)
     */
    public function __construct(private ?Closure $resolver = null) {}

    public function get(string $url, string $accept, int $maxBytes): Response
    {
        [$host, $port, $ip] = $this->assertPublicUrl($url);

        $response = Http::timeout(10)
            ->connectTimeout(5)
            ->withHeaders([
                'User-Agent' => self::USER_AGENT,
                'Accept' => $accept,
                'Accept-Language' => 'uk-UA,uk;q=0.9,en;q=0.8',
            ])
            ->withOptions([
                'allow_redirects' => [
                    'max' => 5,
                    'protocols' => ['http', 'https'],
                    'on_redirect' => fn ($request, $response, UriInterface $uri) => $this->assertPublicUrl((string) $uri),
                ],
                // Прив'язуємо хост до вже перевіреної IP, щоб DNS не підмінили між перевіркою і запитом
                'curl' => [CURLOPT_RESOLVE => [$host.':'.$port.':'.(str_contains($ip, ':') ? "[$ip]" : $ip)]],
                'progress' => function ($downloadTotal, $downloaded) use ($maxBytes) {
                    if ($downloadTotal > $maxBytes || $downloaded > $maxBytes) {
                        throw new RemoteFetchException('Файл завеликий.');
                    }
                },
            ])
            ->get($url);

        if (in_array($response->status(), [401, 403, 429, 503], true)) {
            throw new RemoteFetchException('Цей сайт не дозволяє автоматично читати сторінку. Заповніть поля вручну.');
        }

        if (! $response->successful()) {
            throw new RemoteFetchException('Сайт не віддав сторінку (код '.$response->status().').');
        }

        if (strlen($response->body()) > $maxBytes) {
            throw new RemoteFetchException('Файл завеликий.');
        }

        return $response;
    }

    /**
     * @return array{0: string, 1: int, 2: string} хост, порт, перевірена IP
     */
    public function assertPublicUrl(string $url): array
    {
        $parts = parse_url($url);
        $scheme = strtolower($parts['scheme'] ?? '');
        $host = strtolower(trim($parts['host'] ?? '', '[]'));

        if (! in_array($scheme, ['http', 'https'], true) || $host === '' || isset($parts['user'])) {
            throw new RemoteFetchException('Підтримуються лише посилання http і https.');
        }

        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        if (! in_array($port, [80, 443], true)) {
            throw new RemoteFetchException('Посилання з нестандартним портом не підтримуються.');
        }

        $ips = filter_var($host, FILTER_VALIDATE_IP) ? [$host] : $this->resolve($host);

        if ($ips === []) {
            throw new RemoteFetchException('Не вдалося знайти такий сайт.');
        }

        foreach ($ips as $ip) {
            if (! $this->isPublicIp($ip)) {
                throw new RemoteFetchException('Це посилання веде на внутрішню адресу.');
            }
        }

        return [$host, $port, $ips[0]];
    }

    private function isPublicIp(string $ip): bool
    {
        return filter_var(
            $ip,
            FILTER_VALIDATE_IP,
            FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE | FILTER_FLAG_GLOBAL_RANGE,
        ) !== false;
    }

    /**
     * @return list<string>
     */
    private function resolve(string $host): array
    {
        if ($this->resolver) {
            return ($this->resolver)($host);
        }

        $ipv4 = gethostbynamel($host) ?: [];
        $ipv6 = array_column(@dns_get_record($host, DNS_AAAA) ?: [], 'ipv6');

        return array_values(array_unique([...$ipv4, ...$ipv6]));
    }
}
