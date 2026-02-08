<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rhaima\VoltPanel\Filters\SavedFilterManager;
use Rhaima\VoltPanel\Models\SavedFilter;

class SavedFilterController extends Controller
{
    public function __construct(
        protected SavedFilterManager $filterManager
    ) {}

    public function index(Request $request)
    {
        $resource = $request->get('resource');

        if (!$resource) {
            return response()->json(['error' => 'Resource is required'], 400);
        }

        $filters = $this->filterManager->getSavedFilters($resource);

        return response()->json($filters);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'resource' => 'required|string',
            'name' => 'required|string|max:255',
            'filters' => 'required|array',
            'is_public' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $filter = $this->filterManager->saveFilter(
            $validated['resource'],
            $validated['name'],
            $validated['filters'],
            $validated['is_public'] ?? false,
            $validated['is_default'] ?? false
        );

        return response()->json($filter, 201);
    }

    public function update(Request $request, int $id)
    {
        $filter = SavedFilter::where('id', $id)
            ->where('user_id', auth()->id())
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'filters' => 'sometimes|array',
            'is_public' => 'sometimes|boolean',
            'is_default' => 'sometimes|boolean',
        ]);

        if (isset($validated['is_default']) && $validated['is_default']) {
            $filter->makeDefault();
        }

        $filter->update($validated);

        return response()->json($filter);
    }

    public function destroy(int $id)
    {
        $deleted = $this->filterManager->deleteFilter($id);

        if (!$deleted) {
            return response()->json(['error' => 'Filter not found'], 404);
        }

        return response()->json(['success' => true]);
    }

    public function makeDefault(int $id)
    {
        $success = $this->filterManager->makeDefault($id);

        if (!$success) {
            return response()->json(['error' => 'Filter not found'], 404);
        }

        return response()->json(['success' => true]);
    }
}
