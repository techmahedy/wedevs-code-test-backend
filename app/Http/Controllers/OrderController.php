<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Order;
use App\Helper\Message;
use App\Models\Product;
use App\Models\Deliver;
use App\Helper\Repository;
use Illuminate\Http\Request;
use App\Helper\AuthorizeUser;
use Illuminate\Support\Facades\Gate;
use App\Http\Requests\Order\OrderRequest;
use App\Http\Requests\Order\EditOrderRequest;

class OrderController extends Controller
{   
    use Message, AuthorizeUser;
    
    public $repository;
    
    public function __construct(Repository $repository)
    {  
       if(property_exists($this,'repository')){
           $this->repository = $repository;
       }
    }
    
    public function order(OrderRequest $request)
    {
        if( ! $this->authorizeUser('isBuyer') ) {
            return $this->error('Only buyer can place an order!!', null, 403);
        }

        try {

            if ($request->expectsJson()) {

                $order = new Order();
                $order->user_id = auth()->id();
                $order->product_id = $request->product_id;
                $order->qty = $request->qty;
                $order->price = ($request->qty * $this->repository->price($request->product_id));

                if($this->repository->checkStockExistsOrNot($request->product_id) && $order->save()){
                    $user = User::getAdmin()->first();
                    $user->notify(new \App\Notifications\OrderCompleted($order));
                    return $this->success('Order Created Successfully!', $order, 'order', 200);
                }

                return $this->error('Product is out of stock now!!', null, 422);
            }
                return $this->error('Requested data is not valid!!', null, 422);

            } catch (Throwable $e) {
                Log::info($e);
                return $this->error('Something went wrong!', null, 422);
            }
    }

    public function orderList(Order $order, Request $request)
    {   
       if( $this->authorizeUser('isBuyer') ) {

            $data = $order->with('user:id,name','product:id,name')
                    ->orderBy('id','desc')
                    ->when($request->key, function($query) use($request) {
                        $query->where('status',$request->key);
                    })
                    ->where('user_id',auth()->id())
                    ->get();
        }

        $data = $order->with('user:id,name','product:id,name')
                    ->orderBy('id','desc')
                    ->when($request->key, function($query) use($request) {
                        $query->where('status',$request->key);
                    })
                    ->get();

        return $this->successWithData($data, 'orders');
    }

    public function editOrder(OrderRequest $request, $id)
    {   
        $order = Order::find($id);

        Gate::authorize('update', $order);

        if ($this->repository->status($id) == 'approved' || $this->repository->status($id) == 'reject') {
            return $this->error('The order is already approved or rejected!!', null, 403);
        }

        try {

            if ($request->expectsJson()) {

                $order = Order::find($id);
                $order->user_id = auth()->id();
                $order->product_id = $request->product_id;
                $order->qty = $request->qty;
                $order->price = ($request->qty * $this->repository->price($request->product_id));

                if($this->repository->checkStockExistsOrNot($request->product_id) && $order->save()){
                    return $this->success('Order Updated Successfully!', $order, 'order', 200);
                }

                return $this->error('Product is out of stock now!!', null, 422);
            }
                return $this->error('Requested data is not valid!!', null, 422);

            } catch (Throwable $e) {
                Log::info($e);
                return $this->error('Something went wrong!', null, 422);
            }
    }

    public function orderStatusUpdate(Request $request)
    {
        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('Only admin can update the order status!!', null, 403);
        }

        try {

            if ($request->expectsJson()) {

                $order = Order::find($request->order_id);
                $order->status = $request->status;

                if($this->repository->checkStockExistsOrNot($order->product_id) && $order->save()){
                    return $this->success('Order Updated Successfully!', $order, 'order', 200);
                }

                return $this->error('Product is out of stock now!!', null, 422);
            }
                return $this->error('Requested data is not valid!!', null, 422);

            } catch (Throwable $e) {
                Log::info($e);
                return $this->error('Something went wrong!', null, 422);
            }
    }

    public function deliverOrderList(Deliver $order)
    {
        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('Only admin can see order list!!', null, 403);
        }

        $data = $order->with('user:id,name','product:id,name')
                    ->orderBy('id','desc')
                    ->get();

        return $this->successWithData($data, 'deliveries');
    }

    public function orderDetails($id)
    {   
        try {
            $order = Order::find($id);
            if($order) {
               return $this->successWithData($order->load(['user','product']), 'order');
            }
            return $this->error('Sorry, order with id ' . $id . ' cannot be found!', null, 404);

        } catch (Throwable $th) {
            \Log::info($th);
            return $this->error('Sorry! something went wrong!!', null, 401);
        }
    }
}
