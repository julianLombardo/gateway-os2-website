<?php
/**
 * GatewayOS2 Website - API Search Controller
 *
 * Returns search results as JSON for a given query string.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/SearchService.php';

class ApiSearchController extends Controller
{
    /**
     * Return JSON search results.
     *
     * Query params:
     *   ?q=search+terms
     */
    public function index(): void
    {
        $query = trim($this->request->query('q', ''));

        if ($query === '') {
            $this->json([
                'query'   => '',
                'results' => [],
                'count'   => 0,
            ]);
            return;
        }

        $search  = new SearchService();
        $results = $search->search($query);

        $this->json([
            'query'   => $query,
            'results' => $results,
            'count'   => count($results),
        ]);
    }
}
