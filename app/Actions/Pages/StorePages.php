<?php

namespace App\Actions\Pages;

use App\Models\Page;
use App\Models\Website;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class StorePages
{
    /**
     * Store an array of page URLs for the given website.
     *
     * @param Website $website
     * @param string[] $urls
     * @return int Number of pages inserted
     */
    public function execute(Website $website, array $urls): int
    {
        if (empty($urls)) {
            return 0;
        }

        $rows = [];

        $now = now();

        foreach ($urls as $url) {
            $url = trim($url);

            if ($url === '') {
                continue;
            }

            // Normalize path & slug
            $path = parse_url($url, PHP_URL_PATH) ?: '/';
            $path = '/' . ltrim($path, '/'); // ensure leading slash
            $slug = basename($path);

            $rows[] = [
                'id' => strtolower(Str::ulid()),
                'website_id' => $website->id,
                'url' => $url,
                'path' => $path,
                'slug' => $slug,
                'content' => json_encode([]), // initially empty
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        // Bulk insert, ignore duplicates (unique per website_id + url)
        Page::insertOrIgnore($rows);

        return count($rows);
    }
}
