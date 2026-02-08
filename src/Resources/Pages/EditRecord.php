<?php

namespace Rhaima\VoltPanel\Resources\Pages;

class EditRecord extends Page
{
    protected static ?string $view = 'VoltPanel/Resources/Edit';

    public function getForm(): array
    {
        $resource = static::getResource();
        $form = $resource::form(new \Rhaima\VoltPanel\Forms\Form());

        return $form->toArray();
    }

    public static function getTitle(): string
    {
        return static::$title ?? 'Edit '.static::getResource()::getModelLabel();
    }
}
