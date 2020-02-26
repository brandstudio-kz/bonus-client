<?php

namespace BrandStudio\Bonus\Models;

use Illuminate\Database\Eloquent\Model;
use Bonus;

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

    public static function boot()
    {
        parent::boot();

        static::updating(function($order) {
            $old_status = $order->getOriginal(static::bonusesStatusField());
            if ($order->bonusesCheck()) {
                if (!static::bonusesIsSuccess($old_status) && static::bonusesIsSuccess($order->{static::bonusesStatusField()})) {
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
                } else if (static::bonusesIsSuccess($old_status) && !static::bonusesIsSuccess($order->{static::bonusesStatusField()})) {
                    Bonus::cancelBonus([
                        'bonus' => [
                            'order_id' => $order->id,
                        ],
                    ]);
                }
            }
        });
    }


}
