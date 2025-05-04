<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokFakeChannel extends Model {

    use Sortable;

    protected $table = "tiktok_fake_channel";
    public $timestamps = false;

}
