<?php

namespace App\Http\Controllers;

use App\Models\Banner;
use Illuminate\Http\Request;
use App\Models\Media;
use App\Models\News;
use App\Models\Testimoni;
use App\Models\Resep;
use App\Models\Product;
use Facade\FlareClient\Http\Response;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class MainController extends Controller
{
    public function index()
    {
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

        $products = $products->get()->chunk(8);

        return view("index", [
            'banners'   => Banner::where('publish', 1)->where("mode", "home")->get(),
            'products'  => Product::where("publish", 1)->limit(4)->get()->chunk(4)
        ]);
    }

    public function getResource($id)
    {
        $media = Media::where('mediaId', $id)->first();
        if(!$media)
            return response("File not found", 404);
        
        if(!File::exists($file = app_path("Uploads/" . $media->resultPath)))
            return response($media->resultPath, 404);

        return response(File::get($file), 200)->header("Content-Type", $media->mediaType);
    }

    public function sitemap()
    {
        $sections = [
            [
                'title' => 'Tentang Kami',
                'links' => [
                    ['label' => 'Profil Perusahaan', 'url' => route('about-us')],
                    ['label' => 'Manajemen', 'url' => route('about-us') . '#manajemen'],
                    ['label' => 'Visi & Misi', 'url' => route('about-us') . '#visimisi'],
                ],
            ],
            [
                'title' => 'Bisnis Kami',
                'links' => [
                    // ['label' => 'Produk Kami', 'url' => 'https://www.product.sidoagungfarm.com/', 'external' => true],
                    ['label' => 'Produk Pakan', 'url' => 'https://www.product.sidoagungfarm.com/', 'external' => true],
                    ['label' => 'Kemitraan', 'url' => route('we.be-our-partner')],
                ],
            ],
            [
                'title' => 'Keberlanjutan',
                'links' => [
                    ['label' => 'CSR Summary', 'url' => route('csr.summary')],
                    ['label' => 'Berita CSR', 'url' => route('csr.news')],
                    ['label' => 'Resep', 'url' => route('csr.resep')],
                ],
            ],
            [
                'title' => 'Karir',
                'links' => [
                    ['label' => 'Karir', 'url' => route('we.career')],
                    ['label' => 'Join Us', 'url' => route('we.join-us')],
                ],
            ],
            [
                'title' => 'Hubungi Kami',
                'links' => [
                    ['label' => 'Talk To Us', 'url' => route('we.summary')],
                    ['label' => 'Menjadi Mitra', 'url' => route('we.be-our-partner')],
                ],
            ],
        ];

        return view('sitemap', compact('sections'));
    }

    public function sitemapXml()
    {
        $now = now()->toAtomString();
        $urls = [
            ['loc' => url('/'), 'changefreq' => 'weekly', 'priority' => '1.0', 'lastmod' => $now],
            ['loc' => route('about-us'), 'changefreq' => 'monthly', 'priority' => '0.9', 'lastmod' => $now],
            ['loc' => route('products'), 'changefreq' => 'weekly', 'priority' => '0.9', 'lastmod' => $now],
            ['loc' => route('csr.summary'), 'changefreq' => 'weekly', 'priority' => '0.8', 'lastmod' => $now],
            ['loc' => route('csr.news'), 'changefreq' => 'daily', 'priority' => '0.8', 'lastmod' => $now],
            ['loc' => route('csr.resep'), 'changefreq' => 'weekly', 'priority' => '0.8', 'lastmod' => $now],
            ['loc' => route('we.summary'), 'changefreq' => 'weekly', 'priority' => '0.8', 'lastmod' => $now],
            ['loc' => route('we.join-us'), 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => route('we.be-our-partner'), 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => route('we.career'), 'changefreq' => 'weekly', 'priority' => '0.7', 'lastmod' => $now],
            ['loc' => route('sitemap'), 'changefreq' => 'monthly', 'priority' => '0.6', 'lastmod' => $now],
        ];

        return response()
            ->view('sitemap-xml', compact('urls'))
            ->header('Content-Type', 'application/xml');
    }

    public function testimoni()
    {
        $data = [
            [
                'id' => 6,
                'name' => 'Mela',
                'image' => url("") . '/assets/images/template/products/testimoni/Mela.png',
                'title' => 'product-testimoni-title.6',
                'says' => 'product-testimoni-say.6',
                'show' => 1,
                'created_at' => '26 March 2022 17:25:19',
                'updated_at' => NULL,
                'title_lang' => 'Kampung Parakan',
                'says_lang' => 'Awalnya saya bergabung dengan Belfoods, sebagai reseller, namun seiring dengan berjalannya waktu saya menjadi Agen Belfoods sendiri dan saya tidak menyangka bahwa saya bisa melampaui target yang ditetapkan. Saya senang dan semoga kedepannya saya bisa menjadi Agen Besar.'
            ]
        ];
        return response()->json($data);
    }
}
