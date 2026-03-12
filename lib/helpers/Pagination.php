<?php
/**
 * GatewayOS2 Website - Array Pagination
 *
 * Paginates arrays (e.g. blog posts, users) and returns metadata
 * for rendering pagination controls.
 */

class Pagination
{
    /**
     * Paginate an array of items.
     *
     * @param array $items   The full list of items.
     * @param int   $page    Current page number (1-based).
     * @param int   $perPage Number of items per page.
     * @return array Associative array with pagination data:
     *   - items:   array  Slice of items for the current page
     *   - total:   int    Total number of items
     *   - pages:   int    Total number of pages
     *   - current: int    Current page number
     *   - prev:    int|null  Previous page number or null
     *   - next:    int|null  Next page number or null
     */
    public static function paginate(array $items, int $page, int $perPage = 10): array
    {
        $total = count($items);
        $pages = (int) max(1, ceil($total / $perPage));

        // Clamp current page to valid range
        $page = max(1, min($page, $pages));

        $offset = ($page - 1) * $perPage;
        $slice = array_slice($items, $offset, $perPage);

        return [
            'items'   => $slice,
            'total'   => $total,
            'pages'   => $pages,
            'current' => $page,
            'prev'    => $page > 1 ? $page - 1 : null,
            'next'    => $page < $pages ? $page + 1 : null,
        ];
    }
}
