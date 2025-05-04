<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Zliveclient extends Model {

    use Sortable;

    protected $table = "zliveclient";
    public $timestamps = false;

}
