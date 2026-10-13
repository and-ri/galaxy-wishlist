<?php

namespace App\Http\Controllers;

use App\Services\ProductPageParser;
use App\Services\RemoteFetcher;
use App\Services\RemoteFetchException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Throwable;

class UrlParserController extends Controller
{
    private const MAX_PAGE_BYTES = 3 * 1024 * 1024;

    private const MAX_IMAGE_BYTES = 5 * 1024 * 1024;

    /**
     * Дозволені формати зображень і розширення, з яким їх зберігаємо.
     */
    private const IMAGE_TYPES = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/gif' => 'gif',
        'image/webp' => 'webp',
    ];

    public function __construct(private RemoteFetcher $fetcher) {}

    /**
     * Витягнути дані товару зі сторінки за посиланням
     */
    public function parse(Request $request, ProductPageParser $parser): JsonResponse
    {
        $request->validate(['url' => 'required|url:http,https|max:2048']);

        try {
            $response = $this->fetcher->get(
                $request->url,
                'text/html,application/xhtml+xml;q=0.9,*/*;q=0.5',
                self::MAX_PAGE_BYTES,
            );

            $contentType = $response->header('Content-Type');

            if ($contentType && ! Str::contains($contentType, ['html', 'xml'], ignoreCase: true)) {
                return $this->error('За цим посиланням не вебсторінка.');
            }

            $finalUrl = (string) ($response->effectiveUri() ?? $request->url);

            return response()->json([
                'success' => true,
                'data' => $parser->parse($response->body(), $finalUrl, $contentType),
            ]);
        } catch (Throwable $e) {
            return $this->failure($e, 'Не вдалося відкрити сторінку. Заповніть поля вручну.');
        }
    }

    /**
     * Завантажити зображення товару у сховище
     */
    public function downloadImage(Request $request): JsonResponse
    {
        $request->validate(['image_url' => 'required|url:http,https|max:2048']);

        try {
            $response = $this->fetcher->get($request->image_url, 'image/avif,image/webp,image/*;q=0.8', self::MAX_IMAGE_BYTES);
            $body = $response->body();

            // Тип визначаємо за вмістом, а не за URL чи заголовком: так на диск не потрапить .php чи .html
            $info = @getimagesizefromstring($body);
            $extension = self::IMAGE_TYPES[$info['mime'] ?? ''] ?? null;

            if (! $extension) {
                return $this->error('Зображення має непідтримуваний формат.');
            }

            $path = 'wishes/'.Str::random(40).'.'.$extension;
            Storage::disk('public')->put($path, $body);

            return response()->json([
                'success' => true,
                'path' => $path,
                'url' => asset('storage/'.$path),
            ]);
        } catch (Throwable $e) {
            return $this->failure($e, 'Не вдалося завантажити зображення.');
        }
    }

    private function failure(Throwable $e, string $fallback): JsonResponse
    {
        // Повідомлення RemoteFetchException безпечні для показу; решту лише логуємо
        $known = $e instanceof RemoteFetchException ? $e : ($e->getPrevious() instanceof RemoteFetchException ? $e->getPrevious() : null);

        if (! $known) {
            report($e);
        }

        return $this->error($known?->getMessage() ?? $fallback);
    }

    private function error(string $message): JsonResponse
    {
        return response()->json(['success' => false, 'message' => $message], 422);
    }
}
