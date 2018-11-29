<?php

namespace Dc\Unlocodes\Seeds;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

class UnlocodesTableSeeder extends DcSeeder
{

    public function __construct()
    {
        $this->table = 'unlocodes';
        $this->filename = __DIR__ . '/csvs/unlocode' . (App::runningUnitTests() ? '_testing' : '') . '.csv';
        $this->csv_delimiter = ';';
        $this->mapping = [
            0 => 'countrycode',
            1 => 'placecode',
            2 => 'name',
            3 => 'longitude',
            4 => 'latitude',
            5 => 'subdivision',
            6 => 'status',
            7 => 'date',
            8 => 'IATA',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function seedFromCSV($filename, $deliminator = ",")
    {
        $handle = $this->openCSV($filename);

        // CSV doesn't exist or couldn't be read from.
        if ($handle === false) {
            return [];
        }

        $header = null;
        $row_count = 0;
        $data = [];
        $mapping = $this->mapping ?: [];
        $offset = $this->offset_rows;

        while (($row = fgetcsv($handle, 0, $deliminator)) !== false) {
            // Offset the specified number of rows

            while ($offset > 0) {
                $offset--;
                continue 2;
            }

            $row = $this->readRow($row, $mapping);

            // insert only non-empty rows from the csv file
            if (!$row) {
                continue;
            }

            $row['unlocode'] = $row['countrycode'].$row['placecode'];
            $data[$row_count] = $row;

            // Chunk size reached, insert
            if (++$row_count == $this->insert_chunk_size) {
                $this->insert($data);
                $row_count = 0;
                // clear the data array explicitly when it was inserted so
                // that nothing is left, otherwise a leftover scenario can
                // cause duplicate inserts
                $data = array();
            }
        }

        // Insert any leftover rows
        //check if the data array explicitly if there are any values left to be inserted, if insert them
        if (count($data)) {
            $this->insert($data);
        }

        fclose($handle);

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // echo "Seeding UNLOCodes\n";
        parent::run();
    }
}
