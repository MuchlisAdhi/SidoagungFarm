<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Validator;
use App\Jobs\TicketingJob;
use App\Models\{
    Product,
    ClientQuestion
};
use App\Enums\TicketStatus;

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

        $productId = decrypt(request()->input("productId"));
        $question = ClientQuestion::create([
            'productid' => $productId,
            'name' => request()->input("name"),
            'email' => request()->input("email"),
            'phone' => request()->input("phone"),
            'description' => request()->input("desc"),
            'ticket_status' => TicketStatus::New->value,
        ]);

        TicketingJob::dispatchSync('q1', (string) $question->id);

        return response()->json([
            'code'  => 200,
            'msg'   => "",
            'data'  => []
        ]);
    }
}
