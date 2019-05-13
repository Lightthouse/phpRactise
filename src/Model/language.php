<?php


namespace NTSchool\Model;

use Illuminate\Database\Eloquent\Model;

class language extends Model
{
    public function users(){
        return $this->hasMany(User::class);
    }
}
