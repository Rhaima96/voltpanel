<?php

namespace Rhaima\VoltPanel\ActivityLog;

use Illuminate\Database\Eloquent\Model;

class Activity
{
    protected string $event;
    protected ?Model $subject = null;
    protected ?Model $causer = null;
    protected array $properties = [];
    protected ?string $description = null;
    protected ?string $logName = 'default';

    public function __construct(string $event)
    {
        $this->event = $event;
    }

    public static function make(string $event): static
    {
        return new static($event);
    }

    public static function created(): static
    {
        return new static('created');
    }

    public static function updated(): static
    {
        return new static('updated');
    }

    public static function deleted(): static
    {
        return new static('deleted');
    }

    public static function restored(): static
    {
        return new static('restored');
    }

    public function on(Model $subject): static
    {
        $this->subject = $subject;

        return $this;
    }

    public function by(?Model $causer): static
    {
        $this->causer = $causer;

        return $this;
    }

    public function withProperties(array $properties): static
    {
        $this->properties = $properties;

        return $this;
    }

    public function withProperty(string $key, mixed $value): static
    {
        $this->properties[$key] = $value;

        return $this;
    }

    public function description(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    public function log(string $logName = 'default'): static
    {
        $this->logName = $logName;

        return $this;
    }

    public function save(): void
    {
        $activityModel = config('voltpanel.activity_log.model', \Rhaima\VoltPanel\Models\Activity::class);

        $activityModel::create([
            'log_name' => $this->logName,
            'description' => $this->description ?? $this->event,
            'event' => $this->event,
            'subject_type' => $this->subject ? get_class($this->subject) : null,
            'subject_id' => $this->subject?->getKey(),
            'causer_type' => $this->causer ? get_class($this->causer) : null,
            'causer_id' => $this->causer?->getKey(),
            'properties' => $this->properties,
            'created_at' => now(),
        ]);
    }

    public function toArray(): array
    {
        return [
            'event' => $this->event,
            'description' => $this->description,
            'subject_type' => $this->subject ? get_class($this->subject) : null,
            'subject_id' => $this->subject?->getKey(),
            'causer_type' => $this->causer ? get_class($this->causer) : null,
            'causer_id' => $this->causer?->getKey(),
            'properties' => $this->properties,
            'log_name' => $this->logName,
        ];
    }
}
