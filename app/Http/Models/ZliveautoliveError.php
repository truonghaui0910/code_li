<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class ZliveautoliveError extends Model {

    use Sortable;

    protected $table = "zliveautolive_error";
    public $timestamps = false;

}
