<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostReaction extends Model
{
    protected $fillable = ['post_id','user_id','stamp'];

    // スタンプの定義（順序固定）
    public const STAMPS = [
        0 => ['key'=>'like',     'emoji'=>'👍', 'label'=>'いいね'],
        1 => ['key'=>'empathy',  'emoji'=>'🫶', 'label'=>'共感'],
        2 => ['key'=>'surprise', 'emoji'=>'😮', 'label'=>'驚き'],
        3 => ['key'=>'learned',  'emoji'=>'🎓', 'label'=>'学び'],
        4 => ['key'=>'question', 'emoji'=>'❓', 'label'=>'疑問'],
    ];

    public function scopeWhereStamp($q, int $stamp){ return $q->where('stamp', $stamp); }

    public function post(){ return $this->belongsTo(Post::class); }
    public function user(){ return $this->belongsTo(User::class); }
}
