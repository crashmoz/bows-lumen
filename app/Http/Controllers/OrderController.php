<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Order;
use App\OrderDetail;
use App\Payment;

class OrderController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function index() {
        $date = date('dmy');
        $oldID = Order::where('id', 'like', '%'.$date.'%')->max('id');
        $kd = '';
        if ($oldID) {
            $inc = intval(substr($oldID, 11, 4));
            $inc++;
            $kd = sprintf('%04s', $inc);
        } 
        else 
        {
            $kd = '0001';
        }
        $newID = 'TRX-'.date('dmy').'-'.$kd;
        return response($newID);
    }

    public function store(Request $request)
    {
        DB::transaction(function() use ($request)
        {
            // Make automatic ID
            $date = date('dmy');
            $oldID = Order::where('id', 'like', '%'.$date.'%')->max('id');
            $kd = '';
            if ($oldID) 
            {
                $inc = intval(substr($oldID, 11, 4));
                $inc++;
                $kd = sprintf('%04s', $inc);
            } 
            else 
            {
                $kd = '0001';
            }
            
            $newID = 'TRX-'.$date.'-'.$kd;

            //##########################    Transaction start here  ##########################
            //  Save to ORDER
            $order = New Order;

            $order->id = $newID;
            $order->type_id = $request->order['orderType'];
            if (!empty($request->order['table'])) {
                $order->table_id = $request->order['table'];
            }
            $order->date = $date;
            $order->save();

            //  Save to ORDER DETAIL
            $data = $request->detail;
            for ($i=0; $i < count($data); $i++) {
                $dataDetail[$i] = [
                    'order_id' => $newID,
                    'product_id' => $data[$i]['id'],
                    'quantity' => $data[$i]['quantity'],
                    'price' => $data[$i]['price'],
                    'status' => 1,
                ];
            }
            DB::table('order_detail')->insert($dataDetail);

            //  Save to PAYMENT
            $payment = new Payment;

            $payment->order_id = $newID;
            $payment->payment_type = $request->payment['paymentType'];
            $payment->amount = $request->payment['amount'];
            $payment->save();
        });
        return response('success');
    }
}