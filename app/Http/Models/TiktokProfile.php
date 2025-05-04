<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokProfile extends Model {

    use Sortable;

    protected $table = "tiktok_profile";
    public $timestamps = false;
//    public $sortable = ['id', 'channel_name', 'email', 'views', 'videos', 'subscribes', 'increasing', 'status'];

}
