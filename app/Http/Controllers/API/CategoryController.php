<?php

namespace App\Http\Controllers\API;

use App\Models\Category;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $category = Category::all();

        return response()->json([
            'status' => 200,
            'category' => $category
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
            'meta_title' => 'required|max:191',
            'slug' => 'required|max:191',
            'name' => 'required|max:191'
        ]);

        if ($validator->fails())
        {
           return response()->json([
                'status' => 400,
                'errors' => $validator->errors(),
           ]);
        }


        $category = new Category();
        $category->meta_title = $input['meta_title'];
        $category->meta_keyword = $input['meta_keyword'];
        $category->meta_description = $input['meta_description'];
        $category->slug = $input['slug'];
        $category->name = $input['name'];
        $category->description = $input['description'];
        $category->status = $input['status'] == true ? '1' : '0';
        $category->save();

        return response()->json([
            'status' => 200,
            'message' => 'Category Added Successfully',
        ]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if ($category)
        {
            return response()->json([
                'status' => 200,
                'category' => $category
            ]);
        } else {
            return response()->json([
                'status' => 400,
                'category' => 'No Category Found'
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
            'meta_title' => 'required|max:191',
            'slug' => 'required|max:191',
            'name' => 'required|max:191'
        ]);

        if ($validator->fails())
        {
           return response()->json([
                'status' => 422,
                'errors' => $validator->errors(),
           ]);
        }

        $data = array(
            'meta_title' => $input['meta_title'],
            'meta_keyword' => $input['meta_keyword'],
            'meta_description' => $input['meta_description'],
            'slug' => $input['slug'],
            'name' => $input['name'],
            'description' => $input['description'],
            'status' => $input['status'] == true ? '1' : '0'
        );

        $category = Category::whereId($id)->update($data);

        if ($category)
        {
            return response()->json([
                'status' => 200,
                'message' => 'Category Updated Successfully',
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'Failed to Update Category or Category Not Found',
            ]);
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
        $category = Category::findOrFail($id)->delete();

        if ($category)
        {
            return response()->json([
                'status' => 200,
                'message' => 'Category Deleted Successfully'
            ]);
        } else {
            return response()->json([
                'status' => 404,
                'message' => 'No Category Found'
            ]);
        }
    }
}
