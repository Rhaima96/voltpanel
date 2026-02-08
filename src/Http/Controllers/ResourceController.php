<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Inertia\Inertia;
use Inertia\Response;
use Rhaima\VoltPanel\Facades\VoltPanel;

class ResourceController extends Controller
{
    public function index(Request $request, string $resource): Response|RedirectResponse
    {
        $resourceClass = $this->resolveResource($resource);

        // Check if user can view any records
        if (!$resourceClass::canViewAny()) {
            return redirect()
                ->back()
                ->with('error', 'You are not authorized to view this resource.');
        }

        $query = $resourceClass::getModel()::query();
        $table = $resourceClass::table(new \Rhaima\VoltPanel\Tables\Table());

        // Extract relationships from columns and eager load them
        $relationships = collect($table->toArray()['columns'])
            ->pluck('name')
            ->filter(fn($name) => str_contains($name, '.'))
            ->map(fn($name) => explode('.', $name)[0])
            ->unique()
            ->toArray();

        if (!empty($relationships)) {
            $query->with($relationships);
        }

        // Apply search if provided
        if ($search = $request->input('search')) {
            $searchableColumns = collect($table->toArray()['columns'])
                ->filter(fn($column) => $column['searchable'] ?? false)
                ->pluck('name')
                ->toArray();

            if (!empty($searchableColumns)) {
                $query->where(function ($q) use ($searchableColumns, $search) {
                    foreach ($searchableColumns as $column) {
                        if (str_contains($column, '.')) {
                            // Handle relationship columns
                            [$relation, $field] = explode('.', $column, 2);
                            $q->orWhereHas($relation, function ($q) use ($field, $search) {
                                $q->where($field, 'LIKE', "%{$search}%");
                            });
                        } else {
                            $q->orWhere($column, 'LIKE', "%{$search}%");
                        }
                    }
                });
            }
        }

        // Apply filters if provided
        $filterValues = $request->input('filters', []);
        $tableFilters = $table->getFilters();

        foreach ($tableFilters as $filter) {
            $filterValue = $filterValues[$filter->getName()] ?? null;

            if ($filterValue !== null && $filterValue !== '') {
                $filter->apply($query, $filterValue);
            }
        }

        // Apply sorting if provided
        if ($sort = $request->input('sort')) {
            $sortableColumns = collect($table->toArray()['columns'])
                ->filter(fn($column) => $column['sortable'] ?? false)
                ->pluck('name')
                ->toArray();

            if (in_array($sort, $sortableColumns)) {
                $direction = $request->input('direction', 'asc');

                if (str_contains($sort, '.')) {
                    // Handle relationship sorting
                    [$relation, $field] = explode('.', $sort, 2);
                    $modelClass = $resourceClass::getModel();
                    $model = new $modelClass;
                    $relationInstance = $model->$relation();
                    $relatedTable = $relationInstance->getRelated()->getTable();

                    $query->join($relatedTable, $model->getTable().'.'.$relationInstance->getForeignKeyName(), '=', $relatedTable.'.'.$relationInstance->getOwnerKeyName())
                        ->orderBy($relatedTable.'.'.$field, in_array($direction, ['asc', 'desc']) ? $direction : 'asc')
                        ->select($model->getTable().'.*');
                } else {
                    $query->orderBy($sort, in_array($direction, ['asc', 'desc']) ? $direction : 'asc');
                }
            }
        }

        return Inertia::render('VoltPanel/Resources/List', [
            'resource' => $resource,
            'title' => $resourceClass::getPluralModelLabel(),
            'table' => $table->toArray(),
            'records' => $query->paginate(),
            'canCreate' => $resourceClass::canCreate(),
            'canExport' => method_exists($resourceClass, 'canExport') ? $resourceClass::canExport() : false,
            'exportFormats' => method_exists($resourceClass, 'getExportFormats') ? $resourceClass::getExportFormats() : [],
            'hasTablePreferences' => $resourceClass::hasTablePreferences(),
            'filters' => [
                'search' => $request->input('search', ''),
                'sort' => $request->input('sort', ''),
                'direction' => $request->input('direction', 'asc'),
                'values' => $request->input('filters', []),
            ],
        ]);
    }

    public function create(string $resource): Response|RedirectResponse
    {
        $resourceClass = $this->resolveResource($resource);

        // Check if user can create records
        if (!$resourceClass::canCreate()) {
            return redirect()
                ->back()
                ->with('error', 'You are not authorized to create this resource.');
        }

        $modelClass = $resourceClass::getModel();
        $form = $resourceClass::form(new \Rhaima\VoltPanel\Forms\Form(null, $modelClass));

        return Inertia::render('VoltPanel/Resources/Create', [
            'resource' => $resource,
            'title' => 'Create '.$resourceClass::getModelLabel(),
            'form' => $form->toArray(),
        ]);
    }

    public function store(Request $request, string $resource): RedirectResponse
    {
        try {
            $resourceClass = $this->resolveResource($resource);

            // Check if user can create records
            if (!$resourceClass::canCreate()) {
                return redirect()
                    ->back()
                    ->with('error', 'You are not authorized to create this resource.');
            }

            $modelClass = $resourceClass::getModel();

            // Get form to identify file upload fields
            $form = $resourceClass::form(new \Rhaima\VoltPanel\Forms\Form(null, $modelClass));

            // Process data and handle file uploads
            $data = $this->processFormData($request, $form);

            $record = $modelClass::create($data);

            return redirect()
                ->route('voltpanel.resources.edit', ['resource' => $resource, 'record' => $record->id])
                ->with('success', $resourceClass::getModelLabel().' created successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to create record: ' . $e->getMessage());
        }
    }

    public function view(string $resource, string|int $record): Response|RedirectResponse
    {
        $resourceClass = $this->resolveResource($resource);
        $modelClass = $resourceClass::getModel();

        $record = $modelClass::findOrFail($record);

        // Check if user can view this record
        if (!$resourceClass::canView($record)) {
            return redirect()
                ->back()
                ->with('error', 'You are not authorized to view this record.');
        }

        $form = $resourceClass::form(new \Rhaima\VoltPanel\Forms\Form($record, $modelClass));

        // Check if form has comments field and eager load comments
        $formArray = $form->toArray();
        $hasCommentsField = $this->hasCommentsFieldRecursive($formArray['components']);

        if ($hasCommentsField && method_exists($record, 'comments')) {
            $record->load(['comments.user']);
        }

        // Prepare record data with relationship IDs
        $recordData = $record->toArray();

        // Extract relationship IDs from form components
        foreach ($formArray['components'] as $component) {
            $this->loadRelationshipData($component, $record, $recordData);
        }

        return Inertia::render('VoltPanel/Resources/View', [
            'resource' => $resource,
            'title' => 'View '.$resourceClass::getModelLabel(),
            'form' => $formArray,
            'record' => $recordData,
            'canEdit' => $resourceClass::canEdit($record),
            'canDelete' => $resourceClass::canDelete($record),
        ]);
    }

    public function edit(string $resource, string|int $record): Response|RedirectResponse
    {
        $resourceClass = $this->resolveResource($resource);
        $modelClass = $resourceClass::getModel();

        $record = $modelClass::findOrFail($record);

        // Check if user can edit this record
        if (!$resourceClass::canEdit($record)) {
            return redirect()
                ->back()
                ->with('error', 'You are not authorized to edit this record.');
        }

        $form = $resourceClass::form(new \Rhaima\VoltPanel\Forms\Form($record, $modelClass));

        // Check if form has comments field and eager load comments
        $formArray = $form->toArray();
        $hasCommentsField = $this->hasCommentsFieldRecursive($formArray['components']);

        if ($hasCommentsField && method_exists($record, 'comments')) {
            $record->load(['comments.user']);
        }

        // Prepare record data with relationship IDs
        $recordData = $record->toArray();

        // Extract relationship IDs from form components
        foreach ($formArray['components'] as $component) {
            $this->loadRelationshipData($component, $record, $recordData);
        }

        return Inertia::render('VoltPanel/Resources/Edit', [
            'resource' => $resource,
            'title' => 'Edit '.$resourceClass::getModelLabel(),
            'form' => $formArray,
            'record' => $recordData,
            'canDelete' => $resourceClass::canDelete($record),
            'modelClass' => $modelClass,
        ]);
    }

    public function update(Request $request, string $resource, string|int $record): RedirectResponse
    {
        try {
            $resourceClass = $this->resolveResource($resource);
            $modelClass = $resourceClass::getModel();

            $record = $modelClass::findOrFail($record);

            // Check if user can edit this record
            if (!$resourceClass::canEdit($record)) {
                return redirect()
                    ->back()
                    ->with('error', 'You are not authorized to edit this record.');
            }

            // Get form to identify file upload fields
            $form = $resourceClass::form(new \Rhaima\VoltPanel\Forms\Form($record, $modelClass));

            // Process data and handle file uploads
            $data = $this->processFormData($request, $form);

            $record->update($data);

            return redirect()
                ->route('voltpanel.resources.index', ['resource' => $resource])
                ->with('success', $resourceClass::getModelLabel().' updated successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'Failed to update record: ' . $e->getMessage());
        }
    }

    public function destroy(string $resource, string|int $record): RedirectResponse
    {
        try {
            $resourceClass = $this->resolveResource($resource);
            $modelClass = $resourceClass::getModel();

            $record = $modelClass::findOrFail($record);

            // Check if user can delete this record
            if (!$resourceClass::canDelete($record)) {
                return redirect()
                    ->back()
                    ->with('error', 'You are not authorized to delete this record.');
            }

            $record->delete();

            return redirect()
                ->route('voltpanel.resources.index', ['resource' => $resource])
                ->with('success', $resourceClass::getModelLabel().' deleted successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to delete record: ' . $e->getMessage());
        }
    }

    public function bulkAction(Request $request, string $resource): RedirectResponse
    {
        try {
            $resourceClass = $this->resolveResource($resource);
            $modelClass = $resourceClass::getModel();

            $request->validate([
                'action' => 'required|string',
                'ids' => 'required|array',
                'ids.*' => 'required',
            ]);

            $actionName = $request->input('action');
            $ids = $request->input('ids');

            // Check if this is a delete action and user has permission
            if ($actionName === 'delete' && !$resourceClass::canDeleteAny()) {
                return redirect()
                    ->route('voltpanel.resources.index', ['resource' => $resource])
                    ->with('error', 'You are not authorized to delete records.');
            }

            // Get records
            $records = $modelClass::whereIn('id', $ids)->get();

            if ($records->isEmpty()) {
                return redirect()
                    ->route('voltpanel.resources.index', ['resource' => $resource])
                    ->with('error', 'No records found.');
            }

            // Get bulk actions from the table
            $table = $resourceClass::table(new \Rhaima\VoltPanel\Tables\Table());
            $bulkActions = $table->getBulkActions();

            // Find the action instance
            $actionInstance = $bulkActions->firstWhere('name', $actionName);

            if (!$actionInstance) {
                return redirect()
                    ->route('voltpanel.resources.index', ['resource' => $resource])
                    ->with('error', "Bulk action '{$actionName}' not found.");
            }

            // Execute the bulk action
            $actionInstance->callBulk($records);

            $successMessage = $actionInstance->successNotification
                ?? ($actionInstance->label ?? 'Action') . ' completed successfully.';

            return redirect()
                ->route('voltpanel.resources.index', ['resource' => $resource])
                ->with('success', $successMessage);
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Failed to execute bulk action: ' . $e->getMessage());
        }
    }

    protected function loadRelationshipData(array $component, $record, array &$recordData): void
    {
        // Handle sections with nested components
        if ($component['type'] === 'section' && isset($component['components'])) {
            foreach ($component['components'] as $nestedComponent) {
                $this->loadRelationshipData($nestedComponent, $record, $recordData);
            }
            return;
        }

        // Handle select fields with relationships
        if ($component['type'] === 'select' && isset($component['relationship'])) {
            $relationshipName = $component['relationship'];

            // Check if the relationship exists on the model
            if (method_exists($record, $relationshipName)) {
                // For multiple select (BelongsToMany, HasMany, etc.)
                if ($component['multiple'] ?? false) {
                    // The name might be like 'tag_ids', but relationship is 'tags'
                    // Extract the IDs from the relationship
                    $relation = $record->{$relationshipName}();
                    $relatedModel = $relation->getRelated();
                    $qualifiedKeyName = $relatedModel->getQualifiedKeyName();

                    $recordData[$component['name']] = $relation->pluck($qualifiedKeyName)->toArray();
                }
                // For single select, the foreign key should already be in the array
            }
        }
    }

    protected function processFormData(Request $request, $form): array
    {
        $data = $request->all();
        $formArray = $form->toArray();

        // Recursively find and process file upload fields
        $this->processFileUploads($formArray['components'], $data, $request);

        return $data;
    }

    protected function processFileUploads(array $components, array &$data, Request $request): void
    {
        foreach ($components as $component) {
            // Handle sections with nested components
            if ($component['type'] === 'section' && isset($component['components'])) {
                $this->processFileUploads($component['components'], $data, $request);
                continue;
            }

            // Handle file upload components
            if ($component['type'] === 'file-upload' && isset($component['name'])) {
                $fieldName = $component['name'];

                // Check if a file was uploaded for this field
                if ($request->hasFile($fieldName)) {
                    $file = $request->file($fieldName);

                    // Determine storage disk, directory, and visibility
                    $disk = $component['disk'] ?? 'public';
                    $directory = $component['directory'] ?? 'uploads';
                    $visibility = $component['visibility'] ?? 'public';

                    // Store the file with visibility
                    $path = $file->store($directory, ['disk' => $disk, 'visibility' => $visibility]);

                    // Replace the UploadedFile object with the stored path
                    $data[$fieldName] = $path;
                } elseif (!isset($data[$fieldName]) || $data[$fieldName] === null) {
                    // Remove the field if no file was uploaded and no existing value
                    unset($data[$fieldName]);
                }
            }
        }
    }

    public function uploadRichEditorFile(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:10240', // 10MB max
        ]);

        $file = $request->file('file');
        $originalName = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        // Generate unique filename
        $uniqueName = uniqid() . '_' . time() . '.' . $extension;

        // Save to storage
        $path = $file->storeAs('rich-editor-attachments', $uniqueName, 'public');

        // Return storage URL and original filename
        return response()->json([
            'url' => '/storage/' . $path,
            'filename' => $originalName,
        ]);
    }

    /**
     * Recursively check if form components contain a comments field
     * (including inside sections and other containers)
     */
    protected function hasCommentsFieldRecursive(array $components): bool
    {
        foreach ($components as $component) {
            // Check if this component is a comments field
            if (isset($component['type']) && $component['type'] === 'comments') {
                return true;
            }

            // If component has nested components (like Section), check recursively
            if (isset($component['components']) && is_array($component['components'])) {
                if ($this->hasCommentsFieldRecursive($component['components'])) {
                    return true;
                }
            }

            // Also check 'schema' key (some components use this instead)
            if (isset($component['schema']) && is_array($component['schema'])) {
                if ($this->hasCommentsFieldRecursive($component['schema'])) {
                    return true;
                }
            }
        }

        return false;
    }

    protected function resolveResource(string $slug): string
    {
        // Get default panel
        $panel = VoltPanel::getDefaultPanel();

        if (!$panel) {
            abort(404, "No panel registered.");
        }

        // Find resource by slug
        foreach ($panel->getResources() as $resourceClass) {
            if ($resourceClass::getSlug() === $slug) {
                return $resourceClass;
            }
        }

        abort(404, "Resource with slug '{$slug}' not found.");
    }
}
