<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignFile extends Model
{
    protected $fillable = ['campaign_id', 'file_name', 'path'];

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }
}
