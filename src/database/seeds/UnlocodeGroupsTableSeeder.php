<?php

namespace Dc\Unlocodes\Seeds;

class UnlocodeGroupsTableSeeder extends DcSeeder
{

    public function __construct()
    {
        $this->table = 'unlocode_groups';
        $this->filename = __DIR__ . '/csvs/unlocode_group.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'name',
            1 => 'type',
            2 => 'description'
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // echo "Seeding UNLOCode group definitions\n";
        parent::run();
    }
}
