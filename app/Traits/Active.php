<?php

namespace App\Traits;

trait Active
{

    public function scopeActive($query)
    {
        $query->where('is_active', '=', 1);
        
        // Only apply display_end_date filter for Job model (not Company)
        if ($query->getModel()->getTable() === 'jobs') {
            $query->where(function($q) {
                $q->whereNull('display_end_date')
                  ->orWhere('display_end_date', '>=', now());
            });
        }
        
        return $query;
    }

}
