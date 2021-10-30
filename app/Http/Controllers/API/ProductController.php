<?php

namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $product = Product::with('category')->get();
        return response()->json([
            'status' => 200,
            'product' => $product
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->input();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'slug' => 'required|max:191',
            'name' => 'required|max:191',
            'meta_title'=>'required|max:191',
            'selling_price'=>'required',
            'original_price'=>'required',
            'quantity' => 'required|max:4',
            'image' => 'required|image|mimes:jpeg,png,jpeg|max:2048',
        ]);

        if ($validator->fails())
        {
           return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
           ]);
        } else {

            $product = new Product();

            if ($request->hasFile('image')) {

                $fileDir = storage_path('app/public');

                $destination_path = $fileDir.'/uploads/product/';

                $getpublic_path = 'storage/uploads/product/';

                $file = $request->file('image');
                $extension = $file->getClientOriginalExtension();
                $filename = $input['slug'].'-'.time().'.'.$extension;
                $file->move($destination_path, $filename);
                $product->image = $getpublic_path.$filename;
            }

                $product->category_id = $input['category_id'];
                $product->slug = $input['slug'];
                $product->name = $input['name'];
                $product->description = $input['description'];
                $product->meta_title = $input['meta_title'];
                $product->meta_keyword = $input['meta_keyword'];
                $product->meta_description = $input['meta_description'];
                $product->selling_price = $input['selling_price'];
                $product->original_price = $input['original_price'];
                $product->quantity = $input['quantity'];
                $product->featured = $input['featured'] == true ? '1':'0';
                $product->popular = $input['popular'] == true ? '1':'0';
                $product->status = $input['status'] == true ? '1':'0';


            $product->save();

            return response()->json([
                'status' => 200,
                'message' => 'Product Added Successfully'
            ]);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);

        if ($product)
        {
            return response()->json([
                'status' => 200,
                'product'=>$product
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Product Found',
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $input = $request->input();

        $validator = Validator::make($request->all(), [
            'category_id' => 'required',
            'slug' => 'required|max:191',
            'name' => 'required|max:191',
            'meta_title'=>'required|max:191',
            'selling_price'=>'required',
            'original_price'=>'required',
            'quantity' => 'required|max:4',
        ]);

        if ($validator->fails())
        {
           return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
           ]);
        } else {

            $product = Product::find($id);

            if ($product){
                if ($request->hasFile('image')) {

                $path = $product->image;

                if(\File::exists(public_path($path))){

                    \File::delete(public_path($path));
                }    
                                       
                    $fileDir = storage_path('app/public');
    
                    $destination_path = $fileDir.'/uploads/product/';
    
                    $getpublic_path = 'storage/uploads/product/';
    
                    $file = $request->file('image');
                    $extension = $file->getClientOriginalExtension();
                    $filename = $input['slug'].'-'.time().'.'.$extension;
                    $file->move($destination_path, $filename);
                    $product->image = $getpublic_path.$filename;
                }
    
                    $product->category_id = $input['category_id'];
                    $product->slug = $input['slug'];
                    $product->name = $input['name'];
                    $product->description = $input['description'];
                    $product->meta_title = $input['meta_title'];
                    $product->meta_keyword = $input['meta_keyword'];
                    $product->meta_description = $input['meta_description'];
                    $product->selling_price = $input['selling_price'];
                    $product->original_price = $input['original_price'];
                    $product->quantity = $input['quantity'];
                    $product->featured = $input['featured'] == true ? '1':'0';
                    $product->popular = $input['popular'] == true ? '1':'0';
                    $product->status = $input['status'] == true ? '1':'0';
    
    
                $product->update();
    
                return response()->json([
                    'status' => 200,
                    'message' => 'Product Updated Successfully'
                ]);    
            } else {
                return response()->json([
                    'status' => 404,
                    'message' => 'Product Not Found'
                ]);
            }
            
        }
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
