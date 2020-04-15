<?php

namespace App\Jobs;

use App\Util\KlaviyoHelper;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class KlaviyoUserAdd implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $data;
    protected $url;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($data, $url)
    {
        $this->data = $data;
        $this->url = $url;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(KlaviyoHelper $klaviyoHelperObj)
    {
        \Log::info("queue has called");
        $response = $klaviyoHelperObj->store($this->url, $this->data);
    }
}
