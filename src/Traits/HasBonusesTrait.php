<?php

namespace BrandStudio\Bonus\Traits;

use BrandStudio\Bonus\Facades\Bonus;

trait HasBonusesTrait
{
    protected $cache_client;

    public function getClientAttribute()
    {
        if ($this->cache_client) {
            return $this->cache_client;
        }

        return $this->cache_client = Bonus::getClient([
            'client' => [
                'phone' => $this->phone,
                'full_name' => $this->full_name,
            ]
        ])['data'] ?? null;
    }

    public function getBonusesAttribute()
    {
        return $this->client ? ($this->client['bonuses'] ?? []) : [];
    }

    public function getBonusAttribute()
    {
        return $this->client ? $this->client['active_bonus'] : 0;
    }

}
