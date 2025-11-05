<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessOutsideDisposition
{
    use Dispatchable, SerializesModels;

    public $title;
    public $process;
    public $status_disposition;
    public $production_batch_id;
    public $message;

    public function __construct($title, $production_batch_id, $process, $status_disposition, $message)
    {
        $this->title = $title;
        $this->process = $process;
        $this->production_batch_id = $production_batch_id;
        $this->status_disposition = $status_disposition;
        $this->message = $message;
    }
}
