<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Command extends Model {

    use Sortable;

    protected $table = "command";
    public $timestamps = false;

}
