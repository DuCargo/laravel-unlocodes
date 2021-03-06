<?php

namespace Dc\Unlocodes\Seeds;

use Dc\Unlocodes\Unlocode;
use frictionlessdata\datapackage\Package;
use frictionlessdata\datapackage\Resources\DefaultResource;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

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
     * Overridden CsvSeeder method so we can use our normal base class
     *
     * @param string $filename Ignored
     * @param string $deliminator Ignored
     * @return array|bool
     * @throws \Exception
     */
    public function seedFromCSV($filename, $deliminator = ",")
    {
        if (empty($this->resource)) {
            throw new \Exception('Datapackage resource not defined');
        }
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
            $row['latitude'] = null;
            if (!empty($row['longitude'])) {
                [$lat, $long] = explode(' ', $row['longitude']);
                $row['latitude'] = (float)$lat / 100;
                $row['longitude'] = (float)$long / 100;
            }
            $data[$row_count] = $row;

            // Chunk size reached, insert
            if (++$row_count == $this->insert_chunk_size) {
                $this->insert($data);
                $row_count = 0;
                // clear the data array explicitly when it was inserted so
                // that nothing is left, otherwise a leftover scenario can
                // cause duplicate inserts
                $data = [];
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
     * @throws \Exception
     */
    public function insert(array $seedData)
    {
        try {
            return DB::table($this->table)->insert($seedData);
        } catch (\Illuminate\Database\QueryException $e) {
            // Try to create alias for duplicates
            return $this->insertOrAlias($seedData);
        } catch (\Exception $e) {
            Log::error("CSV insert failed: " . $e->getMessage() . " - CSV " . $this->filename);
            throw $e;
        }
    }

    private function insertOrAlias(array $seedData)
    {
        $result = true;
        foreach ($seedData as $row) {
            if (!Unlocode::where(['unlocode' => $row['unlocode']])->exists()) {
                $result = $result && Unlocode::insert($row);
            } else {
                $result = $result && !empty(Unlocode::where(['unlocode' => $row['unlocode']])->first()->aliases()->create(
                        [
                            'unlocode' => $row['unlocode'],
                            'alias' => $row['name'],
                        ]
                    ));
            }
        }
        return $result;
    }

    /**
     * {@inheritDoc}
     */
    public function run()
    {
        $basepath = 'vendor/datasets/un-locode';
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
