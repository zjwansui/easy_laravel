<?php

namespace Zjwansui\EasyLaravel\Model\Listeners;

use Zjwansui\EasyLaravel\Model\Events\ModelSaving;

class SaveCreatedAt
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param ModelSaving $event
     * @return void
     */
    public function handle(ModelSaving $event)
    {
        $model = $event->model;
        if (!$model->exists && $model->hasAttribute('created_at')) {
            $model->created_at = time();
        }
    }
}
