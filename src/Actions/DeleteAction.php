<?php

namespace Rhaima\VoltPanel\Actions;

class DeleteAction extends Action
{
    public function __construct()
    {
        parent::__construct('delete');

        $this->label('Delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->confirmationTitle('Delete Record')
            ->confirmationText('Are you sure you want to delete this record?')
            ->action(function ($record) {
                $record->delete();
            })
            ->successNotification('Record deleted successfully.');
    }
}
