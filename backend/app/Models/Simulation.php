<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Simulation extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'name',
        'description',
        'configuration',
        'results',
        'notes',
    ];

    protected $casts = [
        'configuration' => 'array',
        'results' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getConfigurationAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setConfigurationAttribute($value)
    {
        $this->attributes['configuration'] = json_encode($value);
    }

    public function getResultsAttribute($value)
    {
        return json_decode($value, true) ?? [];
    }

    public function setResultsAttribute($value)
    {
        $this->attributes['results'] = json_encode($value);
    }
}
