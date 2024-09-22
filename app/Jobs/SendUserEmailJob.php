<?php

namespace App\Jobs;

use App\Mail\MailUser;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class SendUserEmailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $user;
    protected $operate;

    public function __construct($user, $operate)
    {
        $this->user = $user;
        $this->operate = $operate;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->user->email)->send(new MailUser($this->user, $this->operate));
    }
}
