<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    protected $fillable = [
        'name', 'from', 'to', 'total_budget', 'daily_budget',
    ];

    public function creatives()
    {
        return $this->hasMany(CampaignFile::class);
    }
}
