<?php

namespace App\Http\Models;

use Illuminate\Database\Eloquent\Model;
use Kyslik\ColumnSortable\Sortable;

class InvoiceVat extends Model {

    use Sortable;

    protected $table = "invoice_vat";
    public $timestamps = false;

}
