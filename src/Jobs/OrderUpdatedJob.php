<?php

namespace BrandStudio\Bonus\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use BrandStudio\Bonus\Facades\Bonus;

class OrderUpdatedJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    protected $old_status;

    public function __construct($order, $old_status)
    {
        $this->order = $order;
        $this->old_status = $old_status;
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
                // current status - success
                if (!$class::bonusesIsCanceled($this->old_status)) {
                    // previous status - neutral
                    Bonus::unfrozeBonus($this->order);
                }
                Bonus::createBonus($this->order);
            } else if ($class::bonusesIsCanceled($this->order->getBonusStatus())) {
                // current status - canceled
                if ($class::bonusesIsSuccess($this->old_status)) {
                    // previous status - success
                    Bonus::cancelBonus($this->order);
                } else {
                    // previous status - neutral
                    Bonus::unfrozeBonus($this->order);
                }
            } else {
                // current status - neutral
                if ($class::bonusesIsSuccess($this->old_status)) {
                    // previous status - success
                    Bonus::cancelBonus($this->order);
                    Bonus::frozeBonus($this->order);
                } else if ($class::bonusesIsCanceled($this->old_status)) {
                    // previous status - canceled
                    Bonus::frozeBonus($this->order);
                }
            }
        }
    }
}
