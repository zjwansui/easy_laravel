<?php

namespace Zjwansui\EasyLaravel\Model\Listeners;

use Illuminate\Support\Facades\Auth;
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
        Auth::getDefaultDriver();
        $user = Auth::user();
        $uid = $user->id;
        $model = $event->model;
        if (!$model->exists) {
            if ($model->hasAttribute('created_by')) {
                $model->created_by = $uid;
            }
        }
    }
}
