<?php

namespace BrandStudio\Bonus\Models;

use Illuminate\Database\Eloquent\Model;
use BrandStudio\Bonus\Facades\Bonus;

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

    private function getStatus()
    {
        return $this->{static::bonusesStatusField()};
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($order) {
            if ($order->bonusesCheck()) {
                if (static::bonusesIsSuccess($order->getStatus())) {
                    Bonus::createBonus($order);
                } else if (!static::bonusesIsCanceled($order->getStatus())) {
                    Bonus::frozeBonus($order);
                }
            }
        });

        static::updating(function($order) {
            $old_status = $order->getOriginal(static::bonusesStatusField());
            if ($order->bonusesCheck()) {
                if (static::bonusesIsSuccess($order->getStatus())) {
                    // current status - success
                    if (!static::bonusesIsCanceled($old_status)) {
                        // previous status - neutral
                        Bonus::unfrozeBonus($order);
                    }
                    Bonus::createBonus($order);
                } else if (static::bonusesIsCanceled($order->getStatus())) {
                    // current status - canceled
                    if (static::bonusesIsSuccess($old_status)) {
                        // previous status - success
                        Bonus::cancelBonus($order);
                    } else {
                        // previous status - neutral
                        Bonus::unfrozeBonus($order);
                    }
                } else {
                    // current status - neutral
                    if (static::bonusesIsSuccess($old_status)) {
                        // previous status - success
                        Bonus::cancelBonus($order);
                        Bonus::frozeBonus($order);
                    } else if (static::bonusesIsCanceled($old_status)) {
                        // previous status - canceled
                        Bonus::frozeBonus($order);
                    }
                }
            }
        });
    }

}
