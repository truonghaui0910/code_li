<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Bonus extends Model {

    use Sortable;

    protected $table = "bonus";
    public $timestamps = false;

}
