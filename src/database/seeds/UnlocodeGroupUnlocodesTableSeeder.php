<?php

namespace Dc\Unlocodes\Seeds;

use Illuminate\Support\Facades\App;

class UnlocodeGroupUnlocodesTableSeeder extends DcSeeder
{

    public function __construct()
    {
        $this->table = 'unlocode_group_unlocodes';
        $this->filename = __DIR__ . '/csvs/unlocode_group_unlocode' . (App::runningUnitTests() ? '_testing' : '') . '.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'groupname',
            1 => 'unlocode',
        ];
        $this->insert_chunk_size = 512;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // echo "Seeding UNLOCode groups\n";
        parent::run();
    }
}
