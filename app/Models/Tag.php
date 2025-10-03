<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model {
  protected $fillable = ['name','slug','sort_order'];
  public function users(){ return $this->belongsToMany(User::class, 'user_tag')->withTimestamps(); }
}

