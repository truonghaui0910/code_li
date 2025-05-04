<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Zlivecustomer extends Model {

    use Sortable;

    protected $table = "zlivecustomer";
    public $timestamps = false;

}
