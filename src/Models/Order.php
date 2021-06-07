<?php

namespace BrandStudio\Bonus\Models;

use Illuminate\Database\Eloquent\Model;
use BrandStudio\Bonus\Facades\Bonus;

use BrandStudio\Bonus\Jobs\OrderCreatedJob;
use BrandStudio\Bonus\Jobs\OrderUpdatedJob;

abstract class Order extends Model
{

    public abstract function bonusesUsed() : int;
    public abstract function bonusesCashback() : int;
    public abstract function bonusesTotal() : int;
    public abstract function bonusesClient() : array;
    public abstract function bonusesDescription() : string;
    public abstract function bonusesCheck() : bool;
    public abstract static function bonusesStatusField() : string;
    public abstract static function bonusesIsSuccess($status) : bool;
    public abstract static function bonusesIsCanceled($status) : bool;

    public function getBonusStatus()
    {
        return $this->getStatus();
    }

    private function getStatus()
    {
        return $this->{static::bonusesStatusField()};
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($order) {
            OrderCreatedJob::dispatch($order);
        });

        static::updating(function($order) {
            $old_status = $order->getOriginal(static::bonusesStatusField());
            OrderUpdatedJob::dispatch($order, $old_status);
        });
    }

}
