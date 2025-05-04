<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class TiktokProductPin extends Model {

    use Sortable;

    protected $table = "tiktok_product_pin";
    public $timestamps = false;

}
