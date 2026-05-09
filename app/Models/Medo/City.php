<?php

namespace App\Models\Medo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class City extends Model
{
    use HasFactory;

    protected $table = 'medo_cities';

    protected $guarded = [];

    public function getRouteKeyName()
    {
        return 'slug';
    }

    public function province()
    {
        return $this->belongsTo(Province::class);
    }

    public function jobs()
    {
        return $this->hasMany(Job::class, 'city_id');
    }

    public function nearby(int $radiusKm = 75): Collection
    {
        if ($this->latitude === null || $this->longitude === null) {
            return collect();
        }

        return City::selectRaw("*, (
            6371 * acos(
                LEAST(1, GREATEST(-1,
                    cos(radians(?)) * cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) * sin(radians(latitude))
                ))
            )
        ) AS distance_km", [$this->latitude, $this->longitude, $this->latitude])
            ->where('id', '!=', $this->id)
            ->where('province_id', $this->province_id) // same province only
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->having('distance_km', '<=', $radiusKm)
            ->orderBy('distance_km')
            ->get();
    }
}
