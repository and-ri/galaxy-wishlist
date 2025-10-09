<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UrlParserController extends Controller
{
    /**
     * Parse metadata from URL
     */
    public function parse(Request $request)
    {
        $request->validate([
            'url' => 'required|url',
        ]);

        try {
            $url = $request->url;
            
            // Fetch the page content
            $response = Http::timeout(10)
                ->withHeaders([
                    'User-Agent' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
                ])
                ->get($url);

            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to fetch URL content',
                ], 400);
            }

            $html = $response->body();
            
            // Extract metadata
            $data = [
                'title' => $this->extractTitle($html),
                'description' => $this->extractDescription($html),
                'price' => $this->extractPrice($html),
                'currency' => $this->extractCurrency($html),
                'image_url' => $this->extractImage($html, $url),
            ];

            return response()->json([
                'success' => true,
                'data' => $data,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error parsing URL: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Download and save image from URL
     */
    public function downloadImage(Request $request)
    {
        $request->validate([
            'image_url' => 'required|url',
        ]);

        try {
            $imageUrl = $request->image_url;
            
            // Download the image
            $response = Http::timeout(10)->get($imageUrl);
            
            if (!$response->successful()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to download image',
                ], 400);
            }

            // Generate unique filename
            $extension = pathinfo(parse_url($imageUrl, PHP_URL_PATH), PATHINFO_EXTENSION) ?: 'jpg';
            $filename = 'wishes/' . Str::random(40) . '.' . $extension;
            
            // Save to storage
            Storage::disk('public')->put($filename, $response->body());
            
            return response()->json([
                'success' => true,
                'path' => $filename,
                'url' => asset('storage/' . $filename),
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error downloading image: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Extract title from HTML
     */
    private function extractTitle($html)
    {
        // Try Open Graph title
        if (preg_match('/<meta\s+property="og:title"\s+content="([^"]+)"/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Try Twitter title
        if (preg_match('/<meta\s+name="twitter:title"\s+content="([^"]+)"/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Try regular title tag
        if (preg_match('/<title>([^<]+)<\/title>/i', $html, $matches)) {
            return html_entity_decode(trim($matches[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return null;
    }

    /**
     * Extract description from HTML
     */
    private function extractDescription($html)
    {
        // Try Open Graph description
        if (preg_match('/<meta\s+property="og:description"\s+content="([^"]+)"/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Try Twitter description
        if (preg_match('/<meta\s+name="twitter:description"\s+content="([^"]+)"/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        // Try regular meta description
        if (preg_match('/<meta\s+name="description"\s+content="([^"]+)"/i', $html, $matches)) {
            return html_entity_decode($matches[1], ENT_QUOTES | ENT_HTML5, 'UTF-8');
        }
        
        return null;
    }

    /**
     * Extract price from HTML
     */
    private function extractPrice($html)
    {
        // Try Open Graph price
        if (preg_match('/<meta\s+property="product:price:amount"\s+content="([^"]+)"/i', $html, $matches)) {
            return $matches[1];
        }
        
        // Try to find price in common patterns
        if (preg_match('/[₴$€£]?\s*(\d+[\s,]?\d*\.?\d+)\s*[₴$€£]?/u', $html, $matches)) {
            return preg_replace('/[^\d.]/', '', $matches[1]);
        }
        
        return null;
    }

    /**
     * Extract currency from HTML
     */
    private function extractCurrency($html)
    {
        // Try Open Graph currency
        if (preg_match('/<meta\s+property="product:price:currency"\s+content="([^"]+)"/i', $html, $matches)) {
            return strtoupper($matches[1]);
        }
        
        // Try to detect currency symbols
        if (preg_match('/₴|UAH|грн/u', $html)) {
            return 'UAH';
        }
        if (preg_match('/\$|USD/u', $html)) {
            return 'USD';
        }
        if (preg_match('/€|EUR/u', $html)) {
            return 'EUR';
        }
        
        return 'UAH'; // Default currency
    }

    /**
     * Extract image from HTML
     */
    private function extractImage($html, $baseUrl)
    {
        // Try Open Graph image
        if (preg_match('/<meta\s+property="og:image"\s+content="([^"]+)"/i', $html, $matches)) {
            return $this->normalizeImageUrl($matches[1], $baseUrl);
        }
        
        // Try Twitter image
        if (preg_match('/<meta\s+name="twitter:image"\s+content="([^"]+)"/i', $html, $matches)) {
            return $this->normalizeImageUrl($matches[1], $baseUrl);
        }
        
        return null;
    }

    /**
     * Normalize image URL (handle relative URLs)
     */
    private function normalizeImageUrl($imageUrl, $baseUrl)
    {
        if (filter_var($imageUrl, FILTER_VALIDATE_URL)) {
            return $imageUrl;
        }
        
        $parsedBase = parse_url($baseUrl);
        $scheme = $parsedBase['scheme'] ?? 'https';
        $host = $parsedBase['host'] ?? '';
        
        if (str_starts_with($imageUrl, '//')) {
            return $scheme . ':' . $imageUrl;
        }
        
        if (str_starts_with($imageUrl, '/')) {
            return $scheme . '://' . $host . $imageUrl;
        }
        
        return $scheme . '://' . $host . '/' . $imageUrl;
    }
}
