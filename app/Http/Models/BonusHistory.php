<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class BonusHistory extends Model {

    use Sortable;

    protected $table = "bonus_history";
    public $timestamps = false;

}
