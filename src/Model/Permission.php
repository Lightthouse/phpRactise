<?php


namespace NTSchool\Model;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    public function users(){
        return $this->belongsToMany(User::class);
    }
}
