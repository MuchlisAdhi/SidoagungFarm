<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\Banner;

class BannerSummary extends Component
{
    public $mode;
    public function __construct($mode)
    {
        $this->mode = $mode;
    }

    public function render()
    {
        $banner = Banner::where("mode", $this->mode)->where("publish", 1)->first();
        return view('components.banner-summary',[
            'banner'    => $banner
        ]);
    }
}
