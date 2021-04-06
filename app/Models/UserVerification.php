<?php
namespace App\Models;

use \Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager as DB;

class UserVerification extends Model
{
    public $timestamps = false;
    
    public function user() {
        return $this->belongsTo(User::class);
    }
}
