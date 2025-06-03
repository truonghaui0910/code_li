<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Zliveupdate extends Model {

    use Sortable;

    protected $table = "zliveupdate";
    public $timestamps = false;

}
