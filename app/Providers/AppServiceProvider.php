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
                'SELECT DATE_FORMAT(o.created_at,"%d/%m/%Y") order_day,SUM(o.price) + s.price total_price FROM orders o,ships s WHERE o.status = 2 and s.id = o.ship_id GROUP BY order_day'
            );
            $view->with('data',$data);
        });
    }
}
