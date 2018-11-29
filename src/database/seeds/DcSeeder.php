<?php

namespace Dc\Unlocodes\Seeds;

use Flynsarmy\CsvSeeder\CsvSeeder;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DcSeeder extends CsvSeeder
{
    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Recommended when importing larger CSVs
        $wasLogging = DB::logging();
        DB::disableQueryLog();

        // Wipe the table clean before populating and create if needed
        if (Schema::hasTable($this->table)) {
            DB::table($this->table)->delete();
        } else {
            Artisan::call('migrate');
        }

        parent::run();

        if ($wasLogging) {
            DB::enableQueryLog();
        }
    }
}
