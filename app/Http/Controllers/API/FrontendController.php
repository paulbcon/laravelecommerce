<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;

class FrontendController extends Controller
{
    public function category()
    {
        $category = Category::status()->get();

        return response()->json([
            'status' => 200,
            'category' => $category
        ]);

    }

    public function product($slug)
    {
        $category = Category::where('slug',$slug)->status()->first();
        if ($category)
        {
            $product = Product::where('category_id',$category->id)->status()->get();
            if (count($product) > 0)
            {
                return response()->json([
                    'status' => '200',
                    'product_data' => [
                        'product' => $product,
                        'category' =>$category
                    ]
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                    'message' => "No Product Available"
                ]);
            }
        } else {
            return response()->json([
                'status' => '404',
                'message' => "No such Category found"
            ]);
        }
    }

    public function viewproduct($category_slug, $product_slug)
    {
        $category = Category::where('slug',$category_slug)->status()->first();
        if ($category)
        {
            $where = [
                'category_id' => $category->id,
                'slug' => $product_slug
            ];
            $product = Product::where($where)->status()->first();
            if ($product)
            {
                return response()->json([
                    'status' => '200',
                    'product' => $product,
                ]);
            } else {
                return response()->json([
                    'status' => '400',
                    'message' => "No Product Available"
                ]);
            }
        } else {
            return response()->json([
                'status' => '404',
                'message' => "No such Category found"
            ]);
        }
    }
}
