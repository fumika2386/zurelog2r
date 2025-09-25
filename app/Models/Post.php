<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $fillable = ['user_id','title','body','topic_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function topic()
    {
        return $this->belongsTo(\App\Models\Topic::class);
    }

    // app/Models/Post.php （追記）
    public function reactions(){
        return $this->hasMany(\App\Models\PostReaction::class);
    }

    public function userReactionOf(?int $userId){
        if (!$userId) return null;
        return $this->reactions->firstWhere('user_id', $userId) // 事前ロード済みならこれ
            ?? \App\Models\PostReaction::where('post_id',$this->id)->where('user_id',$userId)->first();
    }


}

