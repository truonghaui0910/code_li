<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Zliveaccount extends Model {

    use Sortable;

    protected $table = "zliveaccount";
    public $timestamps = false;

}
