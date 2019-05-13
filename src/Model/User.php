<?php

namespace NTSchool\Model;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    public function permissions(){
        return $this->belongsToMany(Permission::class);
    }
    public function language(){
        return $this->belongsTo(Language::class);
    }
}
