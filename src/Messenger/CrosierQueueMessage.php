<?php

namespace CrosierSource\CrosierLibBaseBundle\Messenger;

class CrosierQueueMessage
{

    public string $queue;

    public $content;

    public function __construct(string $queue, $content)
    {
        $this->queue = $queue;
        $this->content = $content;
    }

}
