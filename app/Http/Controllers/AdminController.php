<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Order;
use App\Models\Sale;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminController extends Controller
{
    public function index(){
        $sales = Order::orderByDesc('id')->where('date',Carbon::now()->format('Y-m-d'))->get();
        $dailySales = Sale::where('date',Carbon::now()->format('Y-m-d'))->sum('total');
        $dailyExpense = Expense::where('date',Carbon::now()->format('Y-m-d'))->sum('amount');
        $dailyP = Sale::where('date',Carbon::now()->format('Y-m-d'))->sum('profit');
        $dailyProfit = $dailyP-$dailyExpense;
        return view('admin.index',[
            'sales'=>$sales,
            'dailySales'=>$dailySales,
            'dailyExpense'=>$dailyExpense,
            'dailyProfit'=>$dailyProfit,
        ]);
    }
    public function expense(){
        $expenses = Expense::orderByDesc('id')->get();
        return view('admin.expense',[
            'expenses'=>$expenses
        ]);
    }
    public function addExpense(){
            return view('admin.addExpense');
    }
    public function storeExpense(Request $request){
        $store = Expense::create([
            'name'=>$request->name,
            'desc'=>$request->desc,
            'amount'=>$request->amount,
            'date'=>$request->date,
            'payment_method'=>$request->paymentMethod,
        ]);
        return redirect(url('expense'))->with('success','EXPENSE ADDED SUCCESS');
    }
    public function expenseEdit($id){
        $expense = Expense::find($id);
        return view('admin.editExpense',[
            'expense'=>$expense
        ]);

    }
    public function eExpense(Request $request){
        $edit = Expense::find($request->expenseId);
        $edit->name = $request->name;
        $edit->desc = $request->desc;
        $edit->amount = $request->amount;
        $edit->date = $request->date;
        $edit->payment_method = $request->paymentMethod;
        $edit->save();
        return redirect(url('expense'))->with('success','EXPENSE EDITED SUCCESS');
    }
    public function viewSale(Request $request){
        $output = "";
        $sales = Sale::where('order_id',$request->id)->orderByDesc('id')->get();
        foreach ($sales as $sale) {
            $output .= '
                                        <tr class="order">

                                            <td class="order-number" data-title="Order">
                                                <a href="*">'.$sale->product_name.'</a>
                                            </td>

                                            <td class="order-date" data-title="Date">
                                                <time datetime="2014-06-12" title="1402562157">'.$sale->quantity.'</time>
                                            </td>

                                            <td class="order-status" data-title="Status">
                                                '.$sale->selling_price.'
                                            </td>
                                            <td class="order-status" data-title="Status">
                                                '.$sale->discount.'(<b>'.$sale->discount_percentage.'%</b>)
                                            </td>

                                            <td class="order-total" data-title="Total">
                                                <span class="amount">'.$sale->total.'</span>
                                            </td>
                                        </tr>


        ';
        }
        return response($output);
    }
    public function viewSaleHeader(Request $request){
        $output = "";
        $sales = Order::find($request->id);
        $output .= '
               <h5 class="modal-title" id="exampleModalLabel">order #'.$sales->id.' <b style="font-size: 20px">'.$sales->phone.'</b></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            ';

        return response($output);
    }
    public function filterMpesa(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        if (is_null($request->productId)){
            $sales = Order::whereBetween('date', array($start_date, $end_date))->orderByDesc('id')->get();
            $dailySales = Sale::whereBetween('date', array($start_date, $end_date))->sum('total');
            $dailyExpense = Expense::whereBetween('date', array($start_date, $end_date))->sum('amount');
            $dailyP = Sale::whereBetween('date', array($start_date, $end_date))->sum('profit');
            $dailyProfit = $dailyP-$dailyExpense;
            return view('admin.indexFilter',[
                'sales'=>$sales,
                'dailySales'=>$dailySales,
                'dailyExpense'=>$dailyExpense,
                'dailyProfit'=>$dailyProfit,
                'check'=>$request->productId,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
            ]);
        }
        else{
            $sales  = Sales::whereBetween('date', array($start_date, $end_date))->where('barcode',$request->productId)->get();
            $getProfit  = Sales::whereBetween('date', array($start_date, $end_date))->where('barcode',$request->productId)->sum('profit');
            $totalSales  = Sales::whereBetween('date', array($start_date, $end_date))->where('barcode',$request->productId)->sum('total');
            $discount  = Sales::whereBetween('date', array($start_date, $end_date))->where('barcode',$request->productId)->sum('discount');
            $expense  = Hotelexpense::whereBetween('date', array($start_date, $end_date))->sum('amount');
            $products = Stock::all();
            $profit = $getProfit;
            return view('admin.indexFilter',[
                'sales'=>$sales,
                'total'=>$sales->sum('amount'),
                'profit'=>$profit,
                'totalSales'=>$totalSales,
                'start_date'=>$start_date,
                'end_date'=>$end_date,
                'expense'=>$expense,
                'products'=>$products,
                'discount'=>$discount,
                'check'=>$request->productId,

            ]);
        }

    }
    public function quote(){
        return view('admin.quote');
    }
    public function CalTotal(Request $request){
        $output = "";
        $getLatestOrder = Order::latest('id')->first();
        $total = Sale::where('order_id',$getLatestOrder->id)->sum('total');
        $output = '
          <td></td>
                                                        <td class="payment"><h3>Ksh '.$total.'</h3></td>
        ';
        return response($output);
    }
    public function receiptFooter(Request $request){
        $output = "";
        $order = Order::latest('id')->first();
        if ($order->payment_method==1){
            $p = 'Mpesa';
        }
        else{
            $p = 'Cash';

        }
        $output = '
           <table class="summary" cellspacing="0">
                                                    <tbody>
                                                    <tr>
                                                        <td>payment Method:</td>
                                                        <td>'.$p.'</td>
                                                    </tr>
                                                    </tbody>
                                                </table>

        ';
        return response($output);
    }
    public function deleteStock(Request $request){
        $output = "";
        $stockId = Stock::find($request->id);
        $output = '
            <input type="hidden" value="'.$stockId->id.'" name="stockId">
            <div class="modal-title h4">ARE YOU SURE</div> <button type="button" class="btn-close" aria-label="Close" data-bs-dismiss="modal"></button>

        ';
        return response($output);
    }
    public function dStock(Request $request){
        $delete = Stock::find($request->stockId);
        $delete->delete();
        return redirect()->back()->with('success','PRODUCT DELETED SUCCESS');
    }
}
