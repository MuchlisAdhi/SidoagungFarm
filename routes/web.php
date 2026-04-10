<?php

use App\Http\Controllers\{
    MainController,
    AboutController,
    ProductController,
    CsrController,
    WeController
};

use App\Http\Controllers\Admin\{
    AuthController,
    AdminController,
    HomeController,
    AdminProductController,
    CareerController
};

use App\Http\Controllers\Admin\{
    FeedbackController,
    NewsController,
    TestimoniController,
    ResepController,
    EmailConfigController,
    TicketController,
    EmailLogController,
    NavigationAccessController
};

use App\Http\Controllers\Admin\CSR\{
    EnvController,
    SafetyController,
    SosialController
};

use Illuminate\Support\Facades\Route;

Route::controller(MainController::class)->name("main")->group(function (){
    Route::get('/', "index");
    Route::get('/getResource/{id}', "getResource")->name(".getResource");
});

Route::get('/sitemap', [MainController::class, 'sitemap'])->name('sitemap');
Route::get('/sitemap.xml', [MainController::class, 'sitemapXml'])->name('sitemap.xml');

Route::get("/about-us", [AboutController::class, "index"])->name("about-us");

Route::controller(ProductController::class)->name("products")->prefix("products")->group(function(){
    Route::get('/', 'index');

    Route::get('/get-product/{id}', 'get')->name(".get");
    Route::post('/sendFaq', 'faq')->name(".faq");
});

Route::controller(CsrController::class)->name("csr")->prefix("csr")->group(function(){
    Route::redirect('/', '/csr/summary', 301);
    Route::get('/summary', 'summary')->name(".summary");
    Route::get('/news', 'news')->name(".news");
    Route::get('/resep', 'resep')->name(".resep");

    Route::get('/getList', 'getList')->name(".getList");
    Route::get('/getDetail', 'getDetail')->name(".getDetail");
});

Route::controller(WeController::class)->name("we")->prefix("talk-us")->group(function(){
    Route::redirect('/', '/talk-us/summary', 301);
    Route::get('/summary', 'summary')->name(".summary");
    Route::post('/sent-question', 'question')->name(".question");
    Route::get('/join-us', 'joinUs')->name(".join-us");
    Route::get('/be-our-partner', 'beOurPartner')->name(".be-our-partner");
    Route::get('/career/{id?}', 'career')->name(".career");
    Route::get('/career-apply/{id}', 'apply')->name(".career.apply");
    Route::post('/job-apply', 'saveApply')->name(".job-apply");

    Route::post('/join-as-partner', 'joinAsPartner')->name(".join-as-partner");
});

Route::prefix("admin")->group(function (){

    Route::controller(AuthController::class)->group(function (){
        Route::get("/", function(){
            return redirect()->route("login");
        });
        Route::match(["get", "post"], "/login", "login")->name("login");
        Route::get("logout", "logout")->name("logout");
    });

    Route::middleware(['auth'])->name("admin")->group(function (){
        Route::get("/unauthorized", [AuthController::class, "unauthorized"])->name(".unauthorized");

        Route::get("/main", [AdminController::class, "main"])
            ->middleware('permission:nav.home.read')
            ->name(".main");

        Route::controller(AdminController::class)->prefix("users")->name(".users")->group(function(){
            Route::get("/", "userList")->middleware('permission:nav.users.read');
            Route::get("/remove/{id}", "remove")->middleware('permission:nav.users.delete');
            Route::get("/getOne/{id}", "getOne")->middleware('permission:nav.users.read|nav.users.update');
            Route::post("/save", "save")->middleware('permission:nav.users.write|nav.users.update');
        });

        Route::controller(NavigationAccessController::class)->prefix("navigation-access")->name(".navigation-access")->group(function(){
            Route::get("/", "index")->middleware('permission:nav.navigation-access.read');
            Route::get("/get/{id}", "get")->middleware('permission:nav.navigation-access.read');
            Route::post("/save", "save")->middleware('permission:nav.navigation-access.write|nav.navigation-access.update');
            Route::post("/delete", "delete")->middleware('permission:nav.navigation-access.delete');
        });

        Route::controller(HomeController::class)->prefix("home")->name(".home")->group(function(){
            Route::prefix("banner")->name(".banner")->group(function(){
                Route::get("/", "bannerList")->middleware('permission:nav.home.banner.read');
                Route::get("/remove/{id}", "bannerRemove")->middleware('permission:nav.home.banner.delete');
                Route::get("/publish/{id}", "bannerPublish")->middleware('permission:nav.home.banner.update');
                Route::post("/save", "bannerSave")->middleware('permission:nav.home.banner.write|nav.home.banner.update');
            });
            Route::prefix("banner-menu")->name(".banner-menu")->group(function(){
                Route::get("/", "bannerMenuList")->middleware('permission:nav.home.banner-menu.read');
                Route::get("/removeMenu/{id}", "bannerMenuRemove")->middleware('permission:nav.home.banner-menu.delete');
                Route::get("/publishMenu/{id}", "bannerMenuPublish")->middleware('permission:nav.home.banner-menu.update');
                Route::post("/saveMenu", "bannerMenuSave")->middleware('permission:nav.home.banner-menu.write|nav.home.banner-menu.update');
            });
        });

        Route::controller(AdminProductController::class)->prefix("product")->name(".product")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.product.read');
            Route::get("/get/{id}", "get")->middleware('permission:nav.product.read');
            Route::get("/remove/{id}", "remove")->middleware('permission:nav.product.delete');
            Route::get("/publish/{id}", "publish")->middleware('permission:nav.product.update');
            Route::post("/save", "save")->middleware('permission:nav.product.write|nav.product.update');
        });

        Route::controller(FeedbackController::class)->prefix("feedback")->name(".feedback")->group(function(){
            Route::prefix("karir")->name(".karir")->group(function(){
                Route::get("/", "careerList")->middleware('permission:nav.feedback.karir.read');
                Route::get("/applicants/{careerId}", "getApplicants")->middleware('permission:nav.feedback.karir.read');
                Route::get("/getApplicant/{id}", "getApplicant")->middleware('permission:nav.feedback.karir.read');
                Route::get("/approveApp/{id}", "approveApp")->middleware('permission:nav.feedback.karir.update');
                Route::post("/rejectApp", "rejectApp")->middleware('permission:nav.feedback.karir.update');
                Route::get("/export-applicants/{careerId}", "exportApplicants")->name(".export-applicants")->middleware('permission:nav.feedback.karir.export');
                Route::get("/download-cv/{id}", "downloadCV")->middleware('permission:nav.feedback.karir.export');
            });
            Route::prefix("pertanyaan")->name(".pertanyaan")->group(function(){
                Route::get("/", "faqList")->middleware('permission:nav.feedback.pertanyaan.read');
                Route::get("/get", "faqGet")->middleware('permission:nav.feedback.pertanyaan.read');
                Route::match(["get", "post"], "/replied", "faqReplied")->middleware('permission:nav.feedback.pertanyaan.update');
            });
            Route::prefix("mitra")->name(".mitra")->group(function(){
                Route::get("/", "mitraList")->middleware('permission:nav.feedback.mitra.read');
                Route::get("/get/{id}", "mitraGet")->middleware('permission:nav.feedback.mitra.read');
                Route::get("/replied/{id}", "mitraReplied")->middleware('permission:nav.feedback.mitra.update');
            });
        });

        Route::controller(CareerController::class)->prefix("karir")->name(".karir")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.karir.read');
            Route::get("/add", "add")->middleware('permission:nav.karir.write');
            Route::get("/edit/{id}", "edit")->middleware('permission:nav.karir.update');
            Route::get("/form", "form")->middleware('permission:nav.karir.write|nav.karir.update');
            Route::post("/save", "save")->middleware('permission:nav.karir.write|nav.karir.update');
            Route::get("/delete/{id}", "delete")->middleware('permission:nav.karir.delete');
        });

        Route::prefix("csr")->name(".csr")->group(function(){
            Route::controller(EnvController::class)->prefix("env")->name(".env")->group(function(){
                Route::get("/", "list")->middleware('permission:nav.csr.env.read');
                Route::get("/add", "add")->middleware('permission:nav.csr.env.write');
                Route::get("/edit/{id}", "edit")->middleware('permission:nav.csr.env.update');
                Route::get("/form", "form")->middleware('permission:nav.csr.env.write|nav.csr.env.update');
                Route::post("/save", "save")->middleware('permission:nav.csr.env.write|nav.csr.env.update');
                Route::get("/delete/{id}", "delete")->middleware('permission:nav.csr.env.delete');
                Route::get("/publish/{id}", "publish")->middleware('permission:nav.csr.env.update');
            });

            Route::controller(SafetyController::class)->prefix("safety")->name(".safety")->group(function(){
                Route::get("/", "list")->middleware('permission:nav.csr.safety.read');
                Route::get("/add", "add")->middleware('permission:nav.csr.safety.write');
                Route::get("/edit/{id}", "edit")->middleware('permission:nav.csr.safety.update');
                Route::get("/form", "form")->middleware('permission:nav.csr.safety.write|nav.csr.safety.update');
                Route::post("/save", "save")->middleware('permission:nav.csr.safety.write|nav.csr.safety.update');
                Route::get("/delete/{id}", "delete")->middleware('permission:nav.csr.safety.delete');
                Route::get("/publish/{id}", "publish")->middleware('permission:nav.csr.safety.update');
            });

            Route::controller(SosialController::class)->prefix("sosial")->name(".sosial")->group(function(){
                Route::get("/", "list")->middleware('permission:nav.csr.sosial.read');
                Route::get("/add", "add")->middleware('permission:nav.csr.sosial.write');
                Route::get("/edit/{id}", "edit")->middleware('permission:nav.csr.sosial.update');
                Route::get("/form", "form")->middleware('permission:nav.csr.sosial.write|nav.csr.sosial.update');
                Route::post("/save", "save")->middleware('permission:nav.csr.sosial.write|nav.csr.sosial.update');
                Route::get("/delete/{id}", "delete")->middleware('permission:nav.csr.sosial.delete');
                Route::get("/publish/{id}", "publish")->middleware('permission:nav.csr.sosial.update');
            });

        });

        Route::controller(NewsController::class)->prefix("news")->name(".news")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.news.read');
            Route::get("/add", "add")->middleware('permission:nav.news.write');
            Route::get("/edit/{id}", "edit")->middleware('permission:nav.news.update');
            Route::get("/form", "form")->middleware('permission:nav.news.write|nav.news.update');
            Route::post("/save", "save")->middleware('permission:nav.news.write|nav.news.update');
            Route::get("/delete/{id}", "delete")->middleware('permission:nav.news.delete');
            Route::get("/publish/{id}", "publish")->middleware('permission:nav.news.update');
        });

        Route::controller(TestimoniController::class)->prefix("testimoni")->name(".testimoni")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.testimoni.read');
            Route::get("/remove/{id}", "remove")->middleware('permission:nav.testimoni.delete');
            Route::post("/save", "save")->middleware('permission:nav.testimoni.write|nav.testimoni.update');
        });

        Route::controller(ResepController::class)->prefix("resep")->name(".resep")->group(function(){
            Route::get("/", "list");
            Route::get("/add", "add");
            Route::get("/edit/{id}", "edit");
            Route::get("/form", "form");
            Route::post("/save", "save");
            Route::get("/delete/{id}", "delete");
            Route::get("/publish/{id}", "publish");
        });

        Route::controller(TicketController::class)->prefix("ticket")->name(".ticket")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.feedback.ticket.read');
            Route::get("/show/{id}", "show")->middleware('permission:nav.feedback.ticket.read');
            Route::post("/update/{id}", "update")->middleware('permission:nav.feedback.ticket.update');
        });

        Route::controller(EmailConfigController::class)->prefix("email-config")->name(".email-config")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.email-config.read');
            Route::get("/add", "add")->middleware('permission:nav.email-config.write');
            Route::get("/edit/{id}", "edit")->middleware('permission:nav.email-config.update');
            Route::get("/form", "form")->middleware('permission:nav.email-config.write|nav.email-config.update');
            Route::post("/save", "save")->middleware('permission:nav.email-config.write|nav.email-config.update');
            Route::get("/delete/{id}", "delete")->middleware('permission:nav.email-config.delete');
            Route::get("/activate/{id}", "activate")->middleware('permission:nav.email-config.update');
        });

        Route::controller(EmailLogController::class)->prefix("email-log")->name(".email-log")->group(function(){
            Route::get("/", "list")->middleware('permission:nav.email-log.read');
            Route::get("/show/{id}", "show")->middleware('permission:nav.email-log.read');
        });
        
    });
});
