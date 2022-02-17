<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Sale;
use App\Models\Sell;
use App\Models\Stock;
use Carbon\Carbon;
use Illuminate\Http\Request;

class SellController extends Controller
{
    public function index(){
        $stocks = Stock::all();
        $sells = Sell::all();
        $total = $sells->sum('total');
        return view('admin.sell',[
            'stocks'=>$stocks,
            'sells'=>$sells,
            'total'=>$total
        ]);
    }
    public function displayQuantity(Request $request){
        $getP = Stock::find($request->id);
        $output = "";
        $output = '
    <input type="hidden" value="'.$request->id.'" name="productId">
           <div class="field-wrapper">
                        <input type="text" class="form-control" name="quantity" required>
                        <div class="field-placeholder">Quantity</div>
                    </div>
                    <div class="field-wrapper">
                        <input type="text" class="form-control" value="'.$getP->selling_price.'" name="amount">
                        <div class="field-placeholder">Amount</div>
                    </div>
        ';
        return response($output);
    }
    public function sellP(Request $request){
        $product = Stock::find($request->productId);
        $currentQ = $product->quantity;
        $stockBuyingPrice = $product->buying_price;
        $selling = $request->amount;
        $final = $currentQ-$request->quantity;
        $total = $selling*$request->quantity;
        $profit =$selling-$stockBuyingPrice;
        $sell = Sell::create([
            'barcode'=>$product->barcode,
            'product_name'=>$product->product_name,
            'quantity'=>$request->quantity,
            'selling_price'=>$request->amount,
            'total'=>$total,
            'profit'=>$profit,
            'image'=>$product->image,
            'date'=>Carbon::now()->format('Y-m-d'),
        ]);
        $updateStock = Stock::where('id',$product->id)->update(['quantity'=>$final]);
        return redirect()->back()->with('success','PRODUCT ADDED SUCCESS');
    }
    public function delProduct(Request $request){
        $del = Sell::find($request->id);
        $del->delete();
        $stock = Stock::where('barcode',$del->barcode)->first();
        $currentStock = $stock->quantity;
        $final = $currentStock+$del->quantity;
        $updateStock = Stock::where('barcode',$del->barcode)->update(['quantity'=>$final]);
    }
    public function sale(Request $request){
        $output = "";
        $sells = Sell::all();
        $sellTotal = $sells->sum('total');
        if (is_null($request->date)){
            $createOrder = Order::create([
                'payment_method'=>$request->paymentMethod,
                'phone'=>$request->phone,
                'amount'=>$request->amount,
                'total'=>$sellTotal,
                'date'=>Carbon::now()->format('Y-m-d'),
            ]);
        }
        else{
            $createOrder = Order::create([
                'payment_method'=>$request->paymentMethod,
                'phone'=>$request->phone,
                'amount'=>$request->amount,
                'total'=>$sellTotal,
                'date'=>$request->date,
            ]);
        }
        if (!is_null($request->date)){
            foreach ($sells as $sell){
                $sale = Sale::create([
                    'barcode'=>$sell->barcode,
                    'product_name'=>$sell->product_name,
                    'quantity'=>$sell->quantity,
                    'selling_price'=>$sell->selling_price,
                    'date'=>$request->date,
                    'total'=>$sell->total,
                    'profit'=>$sell->profit,
                    'order_id'=>$createOrder->id,
                    'image'=>$sell->image,
                ]);
                $sell->delete();
            }
            $receipts = Sale::where('order_id',$createOrder->id)->orderByDesc('id')->get();
            foreach ($receipts as $receipt){
                $output .='

							     <tr>
                                                    <td data-label="Account">'.$receipt->product_name.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Due Date">'.$receipt->quantity.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Amount">'.$receipt->selling_price.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Amount">'.$receipt->total.'</td>
                                                </tr>

        ';
            }

            return response($output);
        }
        else{
            foreach ($sells as $sell){
                $sale = Sale::create([
                    'barcode'=>$sell->barcode,
                    'product_name'=>$sell->product_name,
                    'quantity'=>$sell->quantity,
                    'selling_price'=>$sell->selling_price,
                    'date'=>Carbon::now()->format('Y-m-d'),
                    'total'=>$sell->total,
                    'profit'=>$sell->profit,
                    'order_id'=>$createOrder->id,
                    'image'=>$sell->image,
                ]);
                $sell->delete();

            }
            $receipts = Sale::where('order_id',$createOrder->id)->orderByDesc('id')->get();
            foreach ($receipts as $receipt){
                $output .='

							     <tr>
                                                    <td data-label="Account">'.$receipt->product_name.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Due Date">'.$receipt->quantity.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Amount">'.$receipt->selling_price.'</td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td></td>
                                                    <td data-label="Amount">'.$receipt->total.'</td>
                                                </tr>

        ';
            }

            return response($output);
        }

    }
}
