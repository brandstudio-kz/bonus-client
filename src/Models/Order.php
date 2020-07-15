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

    public static function bonusesIsCanceled($status) : bool
    {
        return static::bonusesIsSuccess($status);
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($order) {
            if (static::bonusesIsSuccess($order->getStatus())) {
                $order->createBonus();
            } else if (!static::bonusesIsCanceled()) {
                $order->frozeBonus();
            }
        });

        static::updating(function($order) {
            $old_status = $order->getOriginal(static::bonusesStatusField());
            if ($order->bonusesCheck()) {
                if (!static::bonusesIsSuccess($old_status) && static::bonusesIsSuccess($order->getStatus())) {
                    if (!static::bonusesIsCanceled($old_status)) {
                        $order->unfrozeBonus();
                    }
                    $order->createBonus();
                } else if (!static::bonusesIsSuccess($order->getStatus())) {
                    if (static::bonusesIsSuccess($old_status)) {
                        $order->cancelBonus();
                    } else {
                        $order->unfrozeBonus();
                    }
                }
            }

        });
    }

    private function createBonus()
    {
        $response = Bonus::createBonus([
            'client' => $order->bonusesClient(),
            'bonus' => [
                'order_id' => $order->id,
                'order' => $order->bonusesDescription(),
                'total' => $order->bonusesTotal(),
                'cashback' => $order->bonusesCashback(),
                'used' => $order->bonusesUsed(),
            ],
        ]);
    }

    private function cancelBonus()
    {
        $response = Bonus::cancelBonus([
            'bonus' => [
                'order_id' => $order->id,
            ],
        ]);
    }

    private function frozeBonus()
    {
        $response = Bonus::frozeBonus([
            'client' => $order->bonusesClient(),
            'bonus' => [
                'used' => $order->bonusesUsed(),
            ],
        ]);
    }

    private function unfrozeBonus()
    {
        $response = Bonus::unfrozeBonus([
            'client' => $order->bonusesClient(),
            'bonus' => [
                'used' => $order->bonusesUsed(),
            ],
        ]);
    }

    private function getStatus()
    {
        return $order->{static::bonusesStatusField()};
    }


}
