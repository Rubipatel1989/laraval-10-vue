<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Counter;

class InvoiceController extends Controller
{
     /**
     * @OA\Get(
     *     path="/api/get_all_invoice",
     *     tags={"Get All Data Pawan"},
     *     summary="Finds All Data",
     *     description="No need any parameter",
     *     operationId="get_all_invoice",     *     *     
     *     @OA\Response(
     *         response=401,
     *         description="Invalid status value"
     *     ),   
     * )
     */

    public function get_all_invoice(){
        $invoices = Invoice::with('customer')->orderBy('id', 'DESC')->get();
        return response()->json([
            'invoices' => $invoices
        ],200);
    }

      /**
     * @OA\Get(
     *     path="/api/search_invoice",
     *     tags={"Search Invoice"},
     *     summary="Search Invoice with id",
     *     description="s",
     *     operationId="search_invoice",
     *     @OA\Parameter(
     *         name="s",
     *         in="query",
     *         description="display that id data. If not provided then give all data",
     *         required=true
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid status value"
     *     ),   
     * )
     */

    public function search_invoice(Request $request){
        $search = $request->get('s');
        if($search != null){
            $invoices = Invoice::with('customer')
                    ->where('id','LIKE',"%$search%")
                    ->orderBy('id', 'DESC')
                    ->get();
            return response()->json([
                'invoices' => $invoices
            ],200);
        } else{
            return $this->get_all_invoice();
        }
    }
    public function create_invoice(Request $request){
        $counter = Counter::where('key', 'invoice')->first();
        $random = Counter::where('key', 'invoice')->first();

        $invoice = Invoice::orderBy('id', 'DESC')->first();
        if($invoice){
            $invoice = $invoice->id + 1;
            $counters = $counter->value + $invoice;
        } else{
            $counters = $counter->value;
        }
        $formData = [
            'number' => $counter->prefix.$counters,
            'customer_id' => null,
            'customer' => null,
            'date' => date('Y-m-d'),
            'due_date' => null,
            'reference' => null,
            'discount' => 0,
            'terms_and_conditions' => 'Default Terms & Conditions',
            'items' => [
                [
                    'product_id' => null,
                    'product' => null,
                    'unit_price' => 0,
                    'quantity' => 1
                ],
            ]


        ];
        
        return response()->json($formData,200); 
    }
    public function add_invoice(Request $request){
        $invoiceitem = $request->input("invoice_item");
        $invoicedata['sub_total'] = $request->input("subtotal");
        $invoicedata['total'] = $request->input("total");
        $invoicedata['customer_id'] = $request->input("customer_id");
        $invoicedata['number'] = $request->input("number");
        $invoicedata['date'] = $request->input("date");
        $invoicedata['due_date'] = $request->input("due_date");
        $invoicedata['discount'] = $request->input("discount");
        $invoicedata['reference'] = $request->input("reference");
        $invoicedata['terms_and_conditions'] = $request->input("terms_and_conditions");

        $invoice = Invoice::create($invoicedata);
        foreach(json_decode($invoiceitem) as $item){
            $itemData['product_id'] = $item->id;
            $itemData['invoice_id'] = $invoice->id;
            $itemData['quantity'] = $item->quantity;
            $itemData['unit_price'] = $item->unit_price;

            InvoiceItem::create($itemData);
        }
    }

    public function show_invoice($id){
        $invoice = Invoice::with('customer','invoice_items.product')->find($id);
        return response()->json([
            'invoice' => $invoice
        ],200);
    }

    public function edit_invoice($id){
        $invoice = Invoice::with('customer','invoice_items.product')->find($id);
        return response()->json([
            'invoice' => $invoice
        ],200);
    }

    public function delete_invoice_items($id){
        $invoiceitem = InvoiceItem::findOrFail($id);
        $invoiceitem->delete();
        // return response()->json([
        //     'invoice' => $invoice
        // ],200);
    }
}