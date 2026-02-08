<?php

namespace Rhaima\VoltPanel\Tables\Actions;

class ViewAction extends Action
{
    public ?string $color = 'gray';

    public function getType(): string
    {
        return 'view';
    }
}
