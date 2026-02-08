<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Rhaima\VoltPanel\Models\UserTablePreference;

class TablePreferenceController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'table_identifier' => 'required|string',
            'visible_columns' => 'nullable|array',
            'hidden_columns' => 'nullable|array',
            'column_order' => 'nullable|array',
        ]);

        UserTablePreference::savePreference(
            auth()->id(),
            $validated['table_identifier'],
            $validated
        );

        return response()->json(['success' => true]);
    }

    public function show(Request $request, string $tableIdentifier)
    {
        $preference = UserTablePreference::getPreference(
            auth()->id(),
            $tableIdentifier
        );

        return response()->json($preference?->toArray() ?? []);
    }

    public function destroy(Request $request, string $tableIdentifier)
    {
        UserTablePreference::where('user_id', auth()->id())
            ->where('table_identifier', $tableIdentifier)
            ->delete();

        return response()->json(['success' => true]);
    }
}
