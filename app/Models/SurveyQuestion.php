<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SurveyQuestion extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'question',
        'description',
        'data',
        'survey_id',
    ];

    // relations
    public function survey()
    {
        return $this->belongsTo(Survey::class);
    } // end of survey
}
