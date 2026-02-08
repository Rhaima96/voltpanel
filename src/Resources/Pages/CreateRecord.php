<?php

namespace Rhaima\VoltPanel\Resources\Pages;

class CreateRecord extends Page
{
    protected static ?string $view = 'VoltPanel/Resources/Create';

    public function getForm(): array
    {
        $resource = static::getResource();
        $form = $resource::form(new \Rhaima\VoltPanel\Forms\Form());

        return $form->toArray();
    }

    public static function getTitle(): string
    {
        return static::$title ?? 'Create '.static::getResource()::getModelLabel();
    }
}
