<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\DB;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        view()->composer('layouts.template',function($view){
             $view->with(
                 [
                     'categories' => DB::table('categories')->where('status','=',1)->get()
                 ]
            );
        });
        view()->composer('admin.layouts.index',function($view){
            $data = DB::select(
                'SELECT DATE_FORMAT(o.created_at,"%d/%m/%Y") order_day,SUM(o.total) total_price FROM orders o WHERE o.status = 2 AND day(o.created_at) = day(NOW()) AND month(o.created_at) = month(NOW()) GROUP BY order_day'
            );
            $view->with('data',$data);
        });
    }
}
