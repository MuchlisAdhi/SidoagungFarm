<?php

namespace App\Http\Controllers;

use App\Http\Requests\Web\SubmitProductFaqRequest;
use App\Models\{
    Product
};
use App\Services\WebsiteInquiryService;

class ProductController extends Controller
{
    public function __construct(
        protected WebsiteInquiryService $websiteInquiryService
    ) {}

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

    public function faq(SubmitProductFaqRequest $request)
    {
        $question = $this->websiteInquiryService->submitProductQuestion($request->toPayload());
        $ticketNo = (string) ($question->ticket_no ?: '-');

        return response()->json([
            'code'  => 200,
            'msg'   => "Pertanyaan berhasil dikirim. Nomor tiket Anda: {$ticketNo}",
            'data'  => [
                'ticket_no' => $ticketNo,
            ]
        ]);
    }
}
