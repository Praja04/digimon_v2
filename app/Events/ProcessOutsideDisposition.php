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

    public function __construct(
        $title = null,
        $production_batch_id = null,
        $process  = null,
        $status_disposition = null,
        $message = null,
    ) {
        $this->title = $title;
        $this->production_batch_id = $production_batch_id;
        $this->process = $process;
        $this->status_disposition = $status_disposition;
        $this->message = $message;
    }
}
