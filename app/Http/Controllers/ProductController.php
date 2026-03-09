<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\{
    Product,
    ClientQuestion
};
class ProductController extends Controller
{
    public function index() {
        $category = request()->query('category');
        $find = request()->query('keyword');

        $products = Product::where("publish", 1);

        if($category)
        $products = $products->where("category", $category);

        if($find)
        {
            $products = $products->where(function($where) use ($find){
                $finds = explode(" ", $find);
                for($i = 0; $i < count($finds); $i++)
                {
                    if($i == 0)
                        $where->where("title", "LIKE", "%" . (strtolower($finds[$i])) . "%");
                    else
                        $where->orWhere("title", "LIKE", "%" . (strtolower($finds[$i])) . "%");
                }
            });
        }

        $products = $products->get()->chunk(6);
        return view("products.summary", [
            'list'  => $products,
            'category'  => $category,
            'search'    => $find
        ]);
    }

    public function get($id)
    {
        $id = decrypt($id);
        $find = Product::where("id", $id)->first();
        if(!$find) 
            return response()->json(['message' => "Record's Not Found."], 404);

        return response()->json($find);
    }

    public function faq()
    {
        $validator = Validator::make(request()->all(), [
            'name'  => "required",
            'email' => "required|email",
            'phone' => "required",
            'desc'  => "required"
        ]);

        if($validator->fails())
            return response()->json([
                'code'  => 0,
                'msg'   => $validator->errors()->first(),
                'data'  => []
            ]);

        ClientQuestion::create([
            'productid' => decrypt(request()->input("productId")),
            'name' => request()->input("name"),
            'email' => request()->input("email"),
            'phone' => request()->input("phone"),
            'description' => request()->input("desc")
        ]);

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
