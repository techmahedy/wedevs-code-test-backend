<?php

namespace App\Http\Controllers;

use App\Helper\File;
use App\Helper\Message;
use App\Models\Product;
use App\Helper\Repository;
use Illuminate\Http\Request;
use App\Helper\AuthorizeUser;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\Product\StoreProduct;

class ProductController extends Controller
{   
    use Message, AuthorizeUser, File;
    
    public $repository;
    
    public function __construct(Repository $repository)
    {  
       if(property_exists($this,'repository')){
           $this->repository = $repository;
       }
    }

    public function index(Product $product)
    {   
        $data = $product->with('user:id,name')
                    ->orderBy('id','desc')
                    ->get();

        return $this->successWithData($data, 'products');
    }

    public function show($id)
    {   
        try {
            if( method_exists(Repository::class,'show') ) {
                return $this->repository->show(Product::class, $id);
            }
        } catch (Throwable $th) {
            \Log::info($th);
            return $this->error('Sorry! something went wrong!!', null, 401);
        }
    }

    public function store(StoreProduct $request)
    {   
        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('You are not authorize to store this!!', null, 403);
        }
        try {

            if($files = $request->file('image')) {
                $fileNameToStore = $this->generateImage($files);
                $folder = '/products/images/';
                $filePath = $folder . $fileNameToStore;
            }

            if ($request->expectsJson()) {

                $product = new Product();
                $product->name = $request->name;
                $product->description = $request->description ? $request->description : '';
                $product->price = $request->price;
                $product->qty = $request->qty;
                $product->image = $filePath;
                $product->user_id = auth()->id();

                if($product->save()){
                    $request->image->move(public_path($folder), $fileNameToStore);
                    return $this->success('Product Created Successfully!', $product, 'product', 200);
                }

                return $this->error('The image has invalid image dimensions!', null, 422);
            }
                return $this->errorJsonResponse('Requested data is not valid!!', null, 422);

            } catch (Throwable $e) {
                Log::info($e);
                return $this->error('Something went wrong!', null, 422);
            }
    }

    public function update(Request $request, $id)
    {   
        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('You are not authorize to edit this!!', null, 403);
        }

        try {

            if($files = $request->file('image')) {
                $fileNameToStore = $this->generateImage($files);
                $folder = '/products/images/';
                $filePath = $folder . $fileNameToStore;
            }else{
                $filePath = '';
            }

            if ($request->expectsJson()) {

                $product = Product::find($id);
                $product->name = $request->name;
                $product->description = $request->description ? $request->description : '';
                $product->price = $request->price;
                $product->qty = $request->qty;
                $product->image = $filePath ? $filePath : $product->image;
                $product->user_id = auth()->id();

                if($product->save()){
                    //it would be great if we @unlick() previous image
                    $filePath ? $request->image->move(public_path($folder), $fileNameToStore) : '';
                    return $this->success('Product Updated Successfully!', $product, 'product', 200);
                }

                return $this->error('Something went wrong!', null, 422);
            }
                return $this->error('Requested data is not valid!!', null, 422);

            } catch (Throwable $e) {
                Log::info($e);
                return $this->error('Something went wrong!', null, 422);
            }
    }

    public function destroy($id)
    {   
        if( ! $this->authorizeUser('isAdmin') ) {
            return $this->error('You are not authorize to delete this!!', null, 403);
        }
        try {
            if( method_exists(Repository::class,'delete') ) {
                if( $this->repository->delete(Product::class, $id) ) {
                    return $this->success('Product deleted Successfully!', '', 'product', 200);
                }
                return $this->error('Sorry! something went wrong!!', null, 401);
            }
        } catch (Throwable $th) {
            \Log::info($th);
            return $this->error('Sorry! something went wrong!!', null, 401);
        }
    }
}
