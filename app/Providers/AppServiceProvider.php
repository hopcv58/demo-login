<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Response;
use Request;
use DB;
use Log;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        // Add macro.
        $this->macro();

        // Log sql query.
        if (config('database.db_log')) {
            $this->logSql();
        }
    }

    /**
     * Register any application services.
     * @return void
     */
    public function register()
    {
        // Load helpers.
        foreach (glob(app_path().'/Helpers/*.php') as $filename) {
            /** @noinspection PhpIncludeInspection */
            require_once $filename;
        }
    }

    /**
     * Log sql query.
     */
    private function logSql ()
    {
        DB::listen(function ($query) {
            Log::debug([
                sql_bind($query->sql, $query->bindings),
                $query->time
            ]);
        });
    }

    /*
     * Define macro.
     */
    private function macro ()
    {
        // Add method xml to Response class.
        Response::macro('xml', function(array $vars, $status = 200, array $header = [])
        {
            // Convert array to xml.
            $xml = xmlify($vars,'response');

            // Add Header Content-Type.
            if (empty($header['Content-Type'])) {
                $header['Content-Type'] = 'application/xml';
            }

            // Return response as xml.
            return Response::make($xml, $status, $header);
        });
    }
}
