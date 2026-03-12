<?php
/**
 * GatewayOS2 Website - Search Controller
 *
 * Handles site-wide search queries and renders results.
 */

require_once BASE_DIR . '/lib/core/Controller.php';
require_once BASE_DIR . '/lib/services/SearchService.php';

class SearchController extends Controller
{
    /**
     * Display search results for a given query.
     *
     * Query params:
     *   ?q=search+terms
     */
    public function index(): void
    {
        $query   = trim($this->request->query('q', ''));
        $results = [];

        if ($query !== '') {
            $search  = new SearchService();
            $results = $search->search($query);
        }

        $this->view('pages/search', [
            'title'   => $query !== '' ? "\"{$query}\" - Search - GatewayOS2" : 'Search - GatewayOS2',
            'query'   => $query,
            'results' => $results,
        ]);
    }
}
