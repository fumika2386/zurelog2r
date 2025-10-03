<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Topic extends Model
{
    protected $fillable = ['title','description','is_published','sort_order'];

    public function posts()
    {
        return $this->hasMany(Post::class);
    }
}
