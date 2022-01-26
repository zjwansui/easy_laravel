<?php

namespace Zjwansui\EasyLaravel\Model\Listeners;

use Illuminate\Validation\ValidationException;
use Zjwansui\EasyLaravel\Model\Events\ModelSaving;

class CheckRules
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param ModelSaving $event
     * @throws ValidationException
     * @throws \Exception
     */
    public function handle(ModelSaving $event)
    {
        if (!$event->model->validate()) {
            throw new \Exception('The given data was invalid.' . $event->model->validator->messages()->toJson(JSON_UNESCAPED_UNICODE));
//            throw new ValidationException($event->model->validator);
        }
    }
}
