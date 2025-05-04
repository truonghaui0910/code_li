<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokTopic extends Model {

    use Sortable;

    protected $table = "tiktok_topic";
    public $timestamps = false;
//    public $sortable = ['id', 'channel_name', 'email', 'views', 'videos', 'subscribes', 'increasing', 'status'];

}
