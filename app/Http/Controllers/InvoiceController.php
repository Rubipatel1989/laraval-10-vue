<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;

class InvoiceController extends Controller
{
    public function get_all_invoice(){
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();
        return response()->json([
            'invoices' => $invoices
        ],200);
    }
}
