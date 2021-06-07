<?php

namespace BrandStudio\Bonus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use BrandStudio\Bonus\Facades\Bonus;


class OrderCreatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;

    public function __construct($order)
    {
        $this->order = $order;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $class = get_class($this->order);
        if ($this->order->bonusesCheck()) {
            if ($class::bonusesIsSuccess($this->order->getBonusStatus())) {
                Bonus::createBonus($this->order);
            } else if (!$class::bonusesIsCanceled($this->order->getBonusStatus())) {
                Bonus::frozeBonus($this->order);
            }
        }
    }
}
