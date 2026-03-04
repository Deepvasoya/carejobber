<?php

namespace App\Traits;

trait Active
{

    public function scopeActive($query)
    {
        return $query->where('is_active', '=', 1)
            ->where(function($q) {
                $q->whereNull('display_end_date')
                  ->orWhere('display_end_date', '>=', now());
            });
    }

}
