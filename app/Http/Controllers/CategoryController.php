<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\CategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use DB;

class CategoryController extends Controller
{
    public function create(CategoryRequest $request){
        $validated = $request->validated();
        $parentCategory = isset($validated['parent_id']) ? $validated['parent_id'] : null;
        DB::beginTransaction();
        try {
            $category = Category::create([
                'name' => $validated['name'],
                'parent_id' => $parentCategory,
            ]);
            DB::commit();
            if ($category) {
                return $this->apiResponse("Record Inserted", [], new CategoryResource($category), [], 201);
            } else {
                return $this->apiResponse("Error while inserting record", [], [], [], 422);
            }
       }catch (\Throwable $th) {
             DB::rollback();
            return $this->apiResponse("Error while inserting record", [], [], [], 422);
        }
    }

    public function getSubcategories($category_id){
        try {
            $subcategories = Category::where('id', $category_id)->first()->subcategories;
            if(count($subcategories) > 0){
                return $this->apiResponse("Subcategories retrieved successfully", [], CategoryResource::collection($subcategories), [], 200);
            }
            return $this->apiResponse("No subcategories found", [], [], [], 422);
        }catch (\Throwable $th) {
           return $this->apiResponse("Error while retrieving records", [], [], [], 422);
       }
    }
}
