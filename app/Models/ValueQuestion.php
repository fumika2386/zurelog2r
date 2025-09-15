<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValueQuestion extends Model
{
    protected $fillable = ['text','sort_order'];

    public function answers()
    {
        return $this->hasMany(ValueAnswer::class, 'question_id');
    }
}
