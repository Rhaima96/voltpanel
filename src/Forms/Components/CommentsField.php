<?php

namespace Rhaima\VoltPanel\Forms\Components;

class CommentsField extends Component
{
    protected string $view = 'voltpanel::components.comments-field';

    public static function make(string $name = 'comments'): static
    {
        $instance = new static($name);
        $instance->label = 'Comments';

        return $instance;
    }

    public function getType(): string
    {
        return 'comments';
    }

    public function toArray(): array
    {
        return array_merge(parent::toArray(), [
            'view' => $this->view,
        ]);
    }

    public function getView(): string
    {
        return $this->view;
    }
}
