<?php

namespace Rhaima\VoltPanel\Tables\Actions;

class EditAction extends Action
{
    public ?string $color = 'primary';

    public function getType(): string
    {
        return 'edit';
    }
}
