<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokDevicePc extends Model {

    use Sortable;

    protected $table = "tiktokdpc";
    public $timestamps = false;

}
