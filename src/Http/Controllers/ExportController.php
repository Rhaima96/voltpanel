<?php

namespace Rhaima\VoltPanel\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Response;
use Rhaima\VoltPanel\Exports\CsvExporter;
use Rhaima\VoltPanel\Exports\XlsxExporter;
use Rhaima\VoltPanel\Exports\PdfExporter;
use Rhaima\VoltPanel\Facades\VoltPanel;

class ExportController extends Controller
{
    public function export(Request $request, string $resource)
    {
        $resourceClass = $this->resolveResource($resource);

        // Check if resource is exportable
        if (!method_exists($resourceClass, 'canExport') || !$resourceClass::canExport()) {
            abort(403, 'This resource cannot be exported.');
        }

        $format = $request->input('format', 'csv');

        // Validate format
        if (!in_array($format, $resourceClass::getExportFormats())) {
            abort(400, 'Invalid export format.');
        }

        // Get export configuration
        $columns = $request->input('columns') ?? $resourceClass::getExportColumns();
        $headers = $resourceClass::getExportHeaders();

        // Filter headers to only include selected columns
        $selectedHeaders = array_filter(
            $headers,
            fn($key) => in_array($key, $columns),
            ARRAY_FILTER_USE_KEY
        );

        // Get records with filters
        $filters = $request->only(['search', 'sort', 'direction']);
        $query = $resourceClass::getExportQuery($filters);

        // Get all records (or use chunking for large datasets)
        $records = $query->get();

        // Export based on format
        $filename = match ($format) {
            'csv' => $this->exportCsv($resourceClass, $records, $columns, $selectedHeaders),
            'xlsx' => $this->exportXlsx($resourceClass, $records, $columns, $selectedHeaders),
            'pdf' => $this->exportPdf($resourceClass, $records, $columns, $selectedHeaders),
            default => abort(400, 'Unsupported export format.'),
        };

        // Return download response
        return $this->download($filename);
    }

    protected function exportCsv(string $resourceClass, $records, array $columns, array $headers): string
    {
        $exporter = new CsvExporter();
        return $exporter->export($resourceClass, $records, $columns, $headers);
    }

    protected function exportXlsx(string $resourceClass, $records, array $columns, array $headers): string
    {
        $exporter = new XlsxExporter();
        return $exporter->export($resourceClass, $records, $columns, $headers);
    }

    protected function exportPdf(string $resourceClass, $records, array $columns, array $headers): string
    {
        $exporter = new PdfExporter();
        return $exporter->export($resourceClass, $records, $columns, $headers);
    }

    public function download(string $filename)
    {
        $filepath = storage_path('app/exports/' . $filename);

        if (!file_exists($filepath)) {
            abort(404, 'Export file not found.');
        }

        return Response::download($filepath)->deleteFileAfterSend(true);
    }

    protected function resolveResource(string $slug): string
    {
        $panel = VoltPanel::getDefaultPanel();

        if (!$panel) {
            abort(404, "No panel registered.");
        }

        foreach ($panel->getResources() as $resourceClass) {
            if ($resourceClass::getSlug() === $slug) {
                return $resourceClass;
            }
        }

        abort(404, "Resource with slug '{$slug}' not found.");
    }
}
