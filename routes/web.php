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
    EmailLogController
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

Route::get("/about-us", [AboutController::class, "index"])->name("about-us");

Route::controller(ProductController::class)->name("products")->prefix("products")->group(function(){
    Route::get('/', 'index');

    Route::get('/get-product/{id}', 'get')->name(".get");
    Route::post('/sendFaq', 'faq')->name(".faq");
});

Route::controller(CsrController::class)->name("csr")->prefix("csr")->group(function(){
    Route::redirect('/', '/summary');
    Route::get('/summary', 'summary')->name(".summary");
    Route::get('/news', 'news')->name(".news");
    Route::get('/resep', 'resep')->name(".resep");

    Route::get('/getList', 'getList')->name(".getList");
    Route::get('/getDetail', 'getDetail')->name(".getDetail");
});

Route::controller(WeController::class)->name("we")->prefix("talk-us")->group(function(){
    Route::redirect('/', '/summary');
    Route::get('/summary', 'summary')->name(".summary");
    Route::post('/sent-question', 'question')->name(".question");
    Route::get('/join-us', 'joinUs')->name(".join-us");
    Route::get('/be-our-partner', 'beOurPartner')->name(".be-our-partner");
    Route::get('/career/{id?}', 'career')->name(".career");
    Route::get('/career-apply/{id}', 'apply')->name(".career.apply");
    Route::post('/job-apply', 'saveApply')->name(".job-apply");

    Route::post('/join-as-partner', 'joinAsPartner')->name(".join-as-partner");
});

Route::prefix("wongelek")->group(function (){

    Route::controller(AuthController::class)->group(function (){
        Route::get("/", function(){
            return redirect()->route("login");
        });
        Route::match(["get", "post"], "/login", "login")->name("login");
        Route::get("logout", "logout")->name("logout");
    });

    Route::middleware(['auth'])->name("admin")->group(function (){
        Route::get("/main", [AdminController::class, "main"])->name(".main");

        Route::controller(AdminController::class)->prefix("users")->name(".users")->group(function(){
            Route::get("/", "userList");
            Route::get("/remove/{id}", "remove");
            Route::get("/getOne/{id}", "getOne");
            Route::post("/save", "save");
        });

        Route::controller(HomeController::class)->prefix("home")->name(".home")->group(function(){
            Route::prefix("banner")->name(".banner")->group(function(){
                Route::get("/", "bannerList");
                Route::get("/remove/{id}", "bannerRemove");
                Route::get("/publish/{id}", "bannerPublish");
                Route::post("/save", "bannerSave");
            });
            Route::prefix("banner-menu")->name(".banner-menu")->group(function(){
                Route::get("/", "bannerMenuList");
                Route::get("/removeMenu/{id}", "bannerMenuRemove");
                Route::get("/publishMenu/{id}", "bannerMenuPublish");
                Route::post("/saveMenu", "bannerMenuSave");
            });
        });

        Route::controller(AdminProductController::class)->prefix("product")->name(".product")->group(function(){
            Route::get("/", "list");
            Route::get("/get/{id}", "get");
            Route::get("/remove/{id}", "remove");
            Route::get("/publish/{id}", "publish");
            Route::post("/save", "save");
        });

        Route::controller(FeedbackController::class)->prefix("feedback")->name(".feedback")->group(function(){
            Route::prefix("karir")->name(".karir")->group(function(){
                Route::get("/", "careerList");
                Route::get("/applicants/{careerId}", "getApplicants");
                Route::get("/getApplicant/{id}", "getApplicant");
                Route::get("/approveApp/{id}", "approveApp");
                Route::post("/rejectApp", "rejectApp");
                Route::get("/download-cv/{id}", "downloadCV");
            });
            Route::prefix("pertanyaan")->name(".pertanyaan")->group(function(){
                Route::get("/", "faqList");
                Route::get("/get", "faqGet");
                Route::match(["get", "post"], "/replied", "faqReplied");
            });
            Route::prefix("mitra")->name(".mitra")->group(function(){
                Route::get("/", "mitraList");
                Route::get("/get/{id}", "mitraGet");
                Route::get("/replied/{id}", "mitraReplied");
            });
        });

        Route::controller(CareerController::class)->prefix("karir")->name(".karir")->group(function(){
            Route::get("/", "list");
            Route::get("/add", "add");
            Route::get("/edit/{id}", "edit");
            Route::get("/form", "form");
            Route::post("/save", "save");
            Route::get("/delete/{id}", "delete");
        });

        Route::prefix("csr")->name(".csr")->group(function(){
            Route::controller(EnvController::class)->prefix("env")->name(".env")->group(function(){
                Route::get("/", "list");
                Route::get("/add", "add");
                Route::get("/edit/{id}", "edit");
                Route::get("/form", "form");
                Route::post("/save", "save");
                Route::get("/delete/{id}", "delete");
                Route::get("/publish/{id}", "publish");
            });

            Route::controller(SafetyController::class)->prefix("safety")->name(".safety")->group(function(){
                Route::get("/", "list");
                Route::get("/add", "add");
                Route::get("/edit/{id}", "edit");
                Route::get("/form", "form");
                Route::post("/save", "save");
                Route::get("/delete/{id}", "delete");
                Route::get("/publish/{id}", "publish");
            });

            Route::controller(SosialController::class)->prefix("sosial")->name(".sosial")->group(function(){
                Route::get("/", "list");
                Route::get("/add", "add");
                Route::get("/edit/{id}", "edit");
                Route::get("/form", "form");
                Route::post("/save", "save");
                Route::get("/delete/{id}", "delete");
                Route::get("/publish/{id}", "publish");
            });

        });

        Route::controller(NewsController::class)->prefix("news")->name(".news")->group(function(){
            Route::get("/", "list");
            Route::get("/add", "add");
            Route::get("/edit/{id}", "edit");
            Route::get("/form", "form");
            Route::post("/save", "save");
            Route::get("/delete/{id}", "delete");
            Route::get("/publish/{id}", "publish");
        });

        Route::controller(TestimoniController::class)->prefix("testimoni")->name(".testimoni")->group(function(){
            Route::get("/", "list");
            Route::get("/remove/{id}", "remove");
            Route::post("/save", "save");
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
            Route::get("/", "list");
            Route::get("/show/{id}", "show");
            Route::post("/update/{id}", "update");
        });

        Route::controller(EmailConfigController::class)->prefix("email-config")->name(".email-config")->group(function(){
            Route::get("/", "list");
            Route::get("/add", "add");
            Route::get("/edit/{id}", "edit");
            Route::get("/form", "form");
            Route::post("/save", "save");
            Route::get("/delete/{id}", "delete");
            Route::get("/activate/{id}", "activate");
        });

        Route::controller(EmailLogController::class)->prefix("email-log")->name(".email-log")->group(function(){
            Route::get("/", "list");
            Route::get("/show/{id}", "show");
        });
        
    });
});
