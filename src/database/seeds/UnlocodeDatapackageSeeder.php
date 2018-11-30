<?php

namespace Dc\Unlocodes\Seeds;

use Dc\Unlocodes\Unlocode;
use Dc\Unlocodes\UnlocodeGroup;
use frictionlessdata\datapackage\Package;
use frictionlessdata\datapackage\Resources\DefaultResource;

class UnlocodeDatapackageSeeder extends DcSeeder
{
    /**
     * @var DefaultResource
     */
    private $resource;

    public function __construct()
    {
        $this->table = 'unlocodes';
        $this->offset_rows = app()->runningUnitTests() ? 109500 : 1;
        $this->insert_chunk_size = app()->runningUnitTests() ? 99 : 500;
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
            if ($rowStr === false || $row === false) {
                break;
            }

            $row = $this->readRow($row, $mapping);

            // insert only non-empty rows from the csv file
            if (!$row) {
                continue;
            }

            $row['unlocode'] = $row['countrycode'] . $row['placecode'];
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

        return $data;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $basepath = "vendor/datasets/un-locode";
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
