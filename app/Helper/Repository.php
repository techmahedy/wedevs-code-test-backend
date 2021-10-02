<?php

namespace App\Helper;

use App\Models\Order;
use App\Helper\Message;
use App\Models\Product;

class Repository {
    
    use Message;

    public function delete(string $model = null, int $id = null) : bool
    {
        $status = $model::where('id',$id)->delete();

        return $status ? true : false;
    }
    
    public function show(string $model = null, int $id = null)
    {
        $product = $model::find($id);

        if($product) {
           return $this->successWithData($product, 'product');
        }

        return $this->error('Sorry, product with id ' . $id . ' cannot be found!', null, 404);
    }

    public function checkStockExistsOrNot($product_id): bool
    {
        $qty = Product::find($product_id)->qty;
        return $qty > 0 ? true : false;
    }

    public function price($product_id)
    {
        $price = Product::find($product_id)->price;
        return $price;
    }

    public function status($order_id)
    {
        $status = Order::find($order_id)->status;
        return $status;
    }
    
    public function order(string $model, int $id)
    {
        $order = $model::find($id);

        if($order) {
           return $this->successWithData($order->load(['user','product']), 'order');
        }

        return $this->error('Sorry, order with id ' . $id . ' cannot be found!', null, 404);
    }

 }