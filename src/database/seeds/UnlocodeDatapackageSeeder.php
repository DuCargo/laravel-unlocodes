<?php

namespace Dc\Unlocodes\Seeds;

use Flynsarmy\CsvSeeder\CsvSeeder;
use frictionlessdata\datapackage\Package;
use frictionlessdata\datapackage\Resources\DefaultResource;

class UnlocodeDatapackageSeeder extends CsvSeeder
{

    /**
     * @var DefaultResource
     */
    private $resource;

    public function __construct()
    {
        $this->table = 'unlocodes';
        $this->offset_rows = 107800;
        $this->insert_chunk_size = 128;
    }

    /**
     * {@inheritDoc}
     */
    public function seedFromCSV($filename, $deliminator = ",")
    {
        $row_count = 0;
        $data = [];
        $mapping = $this->mapping ?: [];
        $offset = $this->offset_rows;

        foreach ($this->resource as $rowStr) {
            // Offset the specified number of rows
            if ($offset > 0) {
                $offset--;
                continue;
            }

            $row = str_getcsv($rowStr, $deliminator);
            if ($row === false) {
                break;
            }

            // No mapping specified - grab the first CSV row and use it
            if (!$mapping) {
                $mapping = $row;
                $mapping[0] = $this->stripUtf8Bom($mapping[0]);

                // skip csv columns that don't exist in the database
                foreach ($mapping as $index => $fieldname) {
                    if (!DB::getSchemaBuilder()->hasColumn($this->table, $fieldname)) {
                        array_pull($mapping, $index);
                    }
                }
            } else {
                $row = $this->readRow($row, $mapping);

                // insert only non-empty rows from the csv file
                if (!$row) {
                    continue;
                }

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
        }

        // Insert any leftover rows
        //check if the data array explicitly if there are any values left to be inserted, if insert them
        if (count($data)) {
            $this->insert($data);
        }

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        // Recommended when importing larger CSVs
        \DB::disableQueryLog();

        // Uncomment the below temporarily to wipe the table clean before populating if needed
        //\DB::table($this->table)->delete();

        $path = dirname(dirname(dirname(__DIR__)));
        $basepath = "{$path}/vendor/datasets/un-locode";
        $package = Package::load("datapackage.json", $basepath);
        $this->resource = $package->resource("code-list");

        $this->discoverMappings($this->resource);

        parent::run();
    }

    /**
     * Reads the data package schema to discover the index at which our columns can be found
     *
     * @param DefaultResource $resource
     */
    private function discoverMappings($resource)
    {
        $fieldMapping = [
            'Country' => 'countrycode',
            'Location' => 'placecode',
            'Name' => 'name',
            'Coordinates' => 'longitude',
            // 4 => 'latitude', // FIXME Split long lat
            'Subdivision' => 'subdivision',
            'Status' => 'status',
            'Date' => 'date',
            'IATA' => 'IATA',
        ];

        foreach ($resource->descriptor()->schema->fields as $i => $field) {
            if (array_key_exists($field->name, $fieldMapping)) {
                $this->mapping[$i] = $fieldMapping[$field->name];
            }
        }
    }
}
