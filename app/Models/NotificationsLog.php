<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationsLog extends Model
{
    public $timestamps = false;
    protected $table = 'notifications_log';
    protected $fillable = ['user_id','channel','recipient','event_type','message','status','sent_at','created_at'];
    protected function casts(): array { return ['sent_at'=>'datetime','created_at'=>'datetime']; }
    public function user() { return $this->belongsTo(User::class); }
}
