<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Package extends Model {

    use Sortable;

    protected $table = "package2";
    public $timestamps = false;
//    public $sortable = ['id', 'channel_name', 'email', 'views', 'videos', 'subscribes', 'increasing', 'status'];

}
