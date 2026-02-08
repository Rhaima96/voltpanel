<?php

namespace Rhaima\VoltPanel\Scheduling;

use Rhaima\VoltPanel\Models\ScheduledExport;
use Illuminate\Support\Facades\Queue;

class ExportScheduler
{
    public function schedule(
        string $resource,
        string $frequency,
        array $filters = [],
        ?string $format = 'csv',
        ?array $recipients = [],
        ?int $userId = null
    ): ScheduledExport {
        return ScheduledExport::create([
            'user_id' => $userId ?? auth()->id(),
            'resource' => $resource,
            'frequency' => $frequency,
            'filters' => $filters,
            'format' => $format,
            'recipients' => $recipients,
            'is_active' => true,
            'next_run_at' => $this->calculateNextRun($frequency),
        ]);
    }

    protected function calculateNextRun(string $frequency): \Carbon\Carbon
    {
        return match($frequency) {
            'daily' => now()->addDay(),
            'weekly' => now()->addWeek(),
            'monthly' => now()->addMonth(),
            default => now()->addDay(),
        };
    }

    public function runDueExports(): void
    {
        $exports = ScheduledExport::where('is_active', true)
            ->where('next_run_at', '<=', now())
            ->get();

        foreach ($exports as $export) {
            Queue::push(new \App\Jobs\RunScheduledExport($export));

            $export->update([
                'next_run_at' => $this->calculateNextRun($export->frequency),
                'last_run_at' => now(),
            ]);
        }
    }
}
