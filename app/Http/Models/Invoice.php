<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class Invoice extends Model {

    use Sortable;

    protected $table = "invoice";
    public $timestamps = false;

}
