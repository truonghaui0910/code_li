<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Zliveautolive extends Model {

    use Sortable;

    protected $table = "zliveautolive";
    public $timestamps = false;

}
