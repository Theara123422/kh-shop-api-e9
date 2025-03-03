<?php

namespace App\Http\Controllers\product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Traits\GeneralResponse;
use Illuminate\Database\QueryException;

class ProductController extends Controller
{
    use GeneralResponse;

    /**
     * list all the products
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $products = \App\Models\Product::all();

        return $this->successReponseWithData(
            "Get all products successfully",
            $products
        );
    }

    /**
     * create new product
     * @param ProductRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(ProductRequest $request){
        try{
            $data = $request->validated();

            if($request->hasFile('image')){
                $fileName = time().'.'.$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }

            $data['image'] = $fileName;

            \App\Models\Product::create($data);

            return $this->successResponse(
                "Product created successfully",
            );
        }catch (QueryException $exception){
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
        catch (ValidationException $exception){
            return $this->errorResponse(
                'Validation Error',
                $exception->getCode()
            );
        }
    }

    /**
     * show the specific product
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show($id)
    {
        $product = \App\Models\Product::findOrFail($id);

        return $this->successReponseWithData(
            "Get product successfully",
            $product
        );
    }

    /**
     * edit the specific product
     * @param ProductRequest $request
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(ProductRequest $request, $id)
    {
        try {
            $product = \App\Models\Product::findOrFail($id);
            $data = $request->validated();

            if($request->hasFile('image')){
                if($product->image){
                    unlink(public_path('images').'/'.$product->image);
                }
                $fileName = time().'.'.$request->file('image')->getClientOriginalExtension();
                $request->file('image')->move(public_path('images'), $fileName);
            }
            $data['image'] = $fileName;

//        dd($data);
            $product->update($data);

            return $this->successResponse(
                "Product updated successfully",
            );
        }catch (\Illuminate\Validation\ValidationException $exception){
            return $this->errorResponse(
                $exception->getMessage(),
                $exception->getCode()
            );
        }
    }

    /**
     * delete the specific product
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $product = \App\Models\Product::findOrFail($id);

        if($product->image){
            unlink(public_path('images').'/'.$product->image);
        }

        $product->delete();

        return $this->successResponse(
            "Product deleted successfully",
        );
    }
}
