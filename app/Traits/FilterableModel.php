<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FilterableModel
{
    // public function scopeFilter($query, array $filters): void
    // {
    //     $query->when($filters['search'] ?? null, function ($query, $search) {
    //         $query->where(function ($query) use ($search) {
    //             $query->where('name', 'like', '%'.$search.'%');
    //         });
    //     })->when($filters['trashed'] ?? null, function ($query, $trashed) {
    //         if ($trashed === 'with') {
    //             $query->withTrashed();
    //         } elseif ($trashed === 'only') {
    //             $query->onlyTrashed();
    //         }
    //     });
    // }

    public function scopeFilter(Builder $query, array $filters)
    {
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->where($key, $value);
            }
        }
    }

    public function scopeFilterone(Builder $query, array $filters)
{
    $query->where(function ($query) use ($filters) {
        foreach ($filters as $key => $value) {
            if ($value !== null) {
                $query->orWhere($key, $value);
            }
        }
    });
}
}
