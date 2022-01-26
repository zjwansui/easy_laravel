<?php

namespace Zjwansui\EasyLaravel\Model\Listeners;

use Zjwansui\EasyLaravel\Model\Events\ModelSaving;

class SaveCreator
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
        $uid = \App\Services\Common\Auth::id();
        $model = $event->model;
        if (!$model->exists) {
            if ($model->hasAttribute('created_by')) {
                $model->created_by = $uid;
            }

            if ($model->hasAttribute('updated_by')) {
                $model->updated_by = $uid;
            }
        }
    }
}
