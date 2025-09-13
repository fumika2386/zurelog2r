<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ValueAnswer extends Model
{
    protected $fillable = ['user_id','question_id','value'];

    public function question()
    {
        return $this->belongsTo(ValueQuestion::class, 'question_id');
    }
}
