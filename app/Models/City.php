<?php

namespace App\Models;

use App\Traits\TranslationHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    use HasFactory;

    use TranslationHelper;

    protected $guarded = [];

    public $translatedAttributes = ['name'];

    public function states():BelongsTo
    {
        return $this->belongsTo(State::class);
    }

    public function employees():HasMany
    {
        return $this->hasMany(Employee::class);
    }
}
