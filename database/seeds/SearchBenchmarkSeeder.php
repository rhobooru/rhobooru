<?php

use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;

class SearchBenchmarkSeeder extends Seeder
{
    private $csvs_folder = '~/docker/rhobooru/csvs/search_test/';

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        dd('Please run this seeder using the `php artisan migrate:search-benchmark --help` command');
    }

    public function LoadData()
    {
        $now = Carbon::now('utc');

        (new DefaultValuesSeeder)->run();

        $this->loadFile(\App\Models\Tag::class);

        $this->loadFile(\App\Models\TagTranslation::class);

        $this->loadFile(\App\Models\Record::class);

        $this->loadFile(\App\Pivots\RecordTag::class);

        echo "\nCompleted in " . (Carbon::now()->longAbsoluteDiffForHumans($now)) . "\n";
    }

    private function loadFile(string $class)
    {
        echo "\nStarting $class...";

        $table = (new $class)->getTable();

        $query = 
            "LOAD DATA INFILE '/home/csvs/search_test/$table.csv' 
            INTO TABLE $table 
            FIELDS TERMINATED BY ','
            LINES TERMINATED BY '\n'
            IGNORE 1 ROWS;";
        
        $rows = \DB::connection()->getpdo()->exec($query);  

        echo "\rLoaded " . number_format($rows) . " $class..."; 
    }

    public function writeData()
    {
        $now = Carbon::now('utc');

        $tag_count = 60000;
        $record_count = 5000000;
        $min_record_tag_count = 5;
        $max_record_tag_count = 40;
        
        $this->writeTags($tag_count);
        
        $this->writeTagTranslations($tag_count);
        
        $this->writeRecords($record_count);
        
        $this->writeRecordTags($record_count, $tag_count, $min_record_tag_count, $max_record_tag_count);

        echo "Completed in " . (Carbon::now()->longAbsoluteDiffForHumans($now)) . "\n";
        echo "Call `php artisan test:load-search-benchmark` to load this data\n";
    }

    private function createFile(string $class)
    {
        $table = (new $class)->getTable();

        $filename = $table . '.csv';
        $path = $this->csvs_folder . $filename;

        $columns = Schema::getColumnListing($table);
        $columns_string = '';

        foreach($columns as $column)
        {
            $columns_string .= $column . ',';
        }

        $file = fopen($path, "wb");

        fwrite($file, substr($columns_string, 0, -1) . "\n");

        return $file;
    }

    private function writeTags(int $count)
    {
        echo "Writing tags...";

        $file = $this->createFile(\App\Models\Tag::class);

        $tags = '';

        $end_portion = ',2019-11-16 04:03:48,2019-11-16 04:03:48,\N,1,1,\N,\N,0,0' . "\n";

        for($i = 1; $i <= $count; $i++)
        {
            $tags .= $i . $end_portion;
        }

        fwrite($file, $tags);

        fclose($file);
    
        echo "\r" . number_format($count) . " tags          \n";
    }

    private function writeTagTranslations(int $count)
    {
        echo "Writing tag translations...";

        $file = $this->createFile(\App\Models\TagTranslation::class);

        $tag_translations = '';

        for($i = 1; $i <= $count; $i++)
        {
            $tag_translations .= $i . ',' . $i . ',en,' . $i . ',\N' . "\n";
        }

        fwrite($file, $tag_translations);

        fclose($file);
    
        echo "\r" . number_format($count) . " tag translations          \n";
    }

    private function writeRecords(int $count)
    {
        echo "Writing records...";

        $batch_size = 5000;

        $file = $this->createFile(\App\Models\Record::class);

        $records = '';

        $middle_portion = ',2019-11-16 04:03:48,2019-11-16 04:03:48,\N,1,1,\N,1,';
        $end_portion = ',\N,png,0,0,0,0,0,0,0,1,1,0,0,0,0,0,0' . "\n";

        for($j = 1; $j <= $count; $j += $batch_size)
        {
            for($i = $j; $i < $j + $batch_size; $i++)
            {
                $records .= $i . $middle_portion . $i . $end_portion;
            }

            fwrite($file, $records);

            $records = '';
        }

        fclose($file);
    
        echo "\r" . number_format($count) . " records           \n";
    }

    private function writeRecordTags(int $records_count, int $tags_count, int $min_tag_count, int $max_tag_count)
    {
        echo "Writing record-tags...";

        $batch_size = 250000;

        $file = $this->createFile(\App\Pivots\RecordTag::class);

        $total_inserted = 0;
        $to_insert_count = 0;

        $ceil_tag_count = $tags_count - $max_tag_count;

        $record_tags = '';

        for($j = 1; $j < $records_count; $j += $batch_size)
        {
            $number_of_tags_for_this_record = mt_rand($min_tag_count, $max_tag_count);

            for($recordId = $j; $recordId < $j + $batch_size; $recordId++)
            {
                $tags_lower_bound = $recordId;

                if($recordId > $ceil_tag_count)
                {
                    $tags_lower_bound = $recordId - $number_of_tags_for_this_record;
                }

                $tags_upper_bound = $tags_lower_bound + $number_of_tags_for_this_record;

                $total_inserted += $number_of_tags_for_this_record;

                $record_portion = $recordId . ',';

                for($i = $tags_lower_bound; $i < $tags_upper_bound; $i++)
                {
                    $record_tags .= $record_portion . $i . "\n";
                }
            }

            fwrite($file, $record_tags);

            $record_tags = '';
        }

        if(strlen($record_tags) > 0)
        {
            fwrite($file, $record_tags);
        }

        fclose($file);
    
        echo "\r" . number_format($records_count) . " records for " . number_format($total_inserted) . " record-tags            \n";
    }
}