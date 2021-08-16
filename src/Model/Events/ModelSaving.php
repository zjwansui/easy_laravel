<?php

namespace Zjwansui\EasyLaravel\Model\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Zjwansui\EasyLaravel\Model\Base\BaseModel;

class ModelSaving
{
    use InteractsWithSockets, SerializesModels;

    public BaseModel $model;

    public function __construct(BaseModel $model)
    {
        $this->model = $model;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
