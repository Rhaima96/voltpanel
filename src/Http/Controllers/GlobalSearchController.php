<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Rhaima\VoltPanel\Facades\VoltPanel;
use Rhaima\VoltPanel\GlobalSearch\GlobalSearchResults;

class GlobalSearchController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->input('query', '');

        if (empty($query) || strlen($query) < 2) {
            return response()->json([
                'results' => [],
            ]);
        }

        $results = new GlobalSearchResults();
        $panel = VoltPanel::getDefaultPanel();

        if (!$panel) {
            return response()->json(['results' => []]);
        }

        foreach ($panel->getResources() as $resourceClass) {
            if (!$resourceClass::canViewAny()) {
                continue;
            }

            $searchResults = $resourceClass::getGlobalSearchResults($query);

            if ($searchResults->isNotEmpty()) {
                $results->add($resourceClass, $searchResults);
            }
        }

        return response()->json([
            'results' => $results->toArray(),
        ]);
    }
}
