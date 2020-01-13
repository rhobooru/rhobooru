<?php

use App\Models\Tag;
use App\Models\Record;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SearchTestSeeder extends Seeder
{
    private $now;

    const MAX_PARAMETERS_PER_QUERY = 65535;

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->now = Carbon::now('utc')->toDateTimeString();

        (new DefaultValuesSeeder)->run();
        
        $this->seedTags(60000);
        
        $this->seedTagTranslations(60000);
        
        $this->seedRecords(5000000);
        
        $this->seedRecordTags(5, 40);

        echo "\n";
    }

    public function turnOffDBVariables()
    {
        DB::statement('SET GLOBAL unique_checks=0;');
        DB::statement('SET GLOBAL foreign_key_checks=0;'); 
        DB::statement('SET GLOBAL autocommit=0;');
    }

    public function turnOnDBVariables()
    {
        DB::statement('COMMIT;');
        DB::statement('SET GLOBAL autocommit=1;');
        DB::statement('SET GLOBAL unique_checks=1;');
        DB::statement('SET GLOBAL foreign_key_checks=1;');
    }

    public function seedTags(int $count)
    {
        $this->turnOffDBVariables();

        echo "\nStarting tags...";

        $batch_size = 60000;

        $columns = [
            'created_at',
            'updated_at',
            'created_by_user_id',
        ];
        $query_template = 'insert into tags (' . implode(',', $columns) . ') values ';
        $query = $query_template;

        for($i = 0; $i < $count; $i++)
        {
            $query .= '("' .
                $this->now . '","' .
                $this->now . '","' .
                1 . '")';

            if($i % $batch_size == 0)
            {
                DB::insert(DB::raw($query));

                $query = $query_template;
    
                echo "\r" . number_format($i) . " tags                ";
            }
            else
            {
                $query .= ',';
            }
        }

        DB::insert(DB::raw(substr($query, 0, -1)));
    
        echo "\r" . number_format($count) . " tags                ";

        $this->turnOnDBVariables();
    }

    public function seedTagTranslations(int $count)
    {
        $this->turnOffDBVariables();

        echo "\nStarting tag translations...";

        $batch_size = 60000;

        $columns = [
            'tag_id',
            'locale',
            'name',
        ];
        $query_template = 'insert into tag_translations (' . implode(',', $columns) . ') values ';
        $query = $query_template;

        for($i = 0; $i < $count; $i++)
        {
            $query .= '("' .
                $i . '","' .
                'en","' .
                \Str::random(20) . '")';

            if($i % $batch_size == 0)
            {
                DB::insert(DB::raw($query));

                $query = $query_template;
    
                echo "\r" . number_format($i) . " tag translations                ";
            }
            else
            {
                $query .= ',';
            }
        }

        DB::insert(DB::raw(substr($query, 0, -1)));
    
        echo "\r" . number_format($count) . " tag translations                ";

        $this->turnOnDBVariables();
    }

    public function seedRecords(int $count)
    {
        $this->turnOffDBVariables();

        echo "\nStarting records...";

        $batch_size = 100000;

        $columns = [
            'created_at',
            'updated_at',
            'created_by_user_id',
            'md5',
            'file_extension',
            'content_rating_id',
            'record_type_id',
            'approved',
        ];
        $query_template = 'insert into records (' . implode(',', $columns) . ') values ';
        $query = $query_template;

        for($i = 0; $i < $count; $i++)
        {
            $query .= '("' .
                $this->now . '","' .
                $this->now . '","' .
                1 . '","' .
                'abc","' .
                'jpg","' .
                1 . '","' .
                1 . '","' .
                1 . '")';

            if($i % $batch_size == 0)
            {
                DB::insert(DB::raw($query));

                $query = $query_template;
    
                echo "\r" . number_format($i) . " records                ";
            }
            else
            {
                $query .= ',';
            }
        }

        DB::insert(DB::raw(substr($query, 0, -1)));
    
        echo "\r" . number_format($count) . " records                ";

        $this->turnOnDBVariables();
    }

    public function seedRecordTags(int $min_tag_count, int $max_tag_count)
    {
        $this->turnOffDBVariables();

        $indices = $this->dropRecordTagIndices();

        echo "\nStarting record-tags...";

        $batch_size = 1000000;

        $tags = Tag::all()->pluck('id');

        $columns = [
            'record_id',
            'tag_id',
        ];
        $query_template = 'insert into record_tag (' . implode(',', $columns) . ') values ';
        $query = $query_template;
        $to_insert_count = 0;

        $records_count = \App\Models\Record::count();
        $inserted_so_far = 0;

        $half_tag_point_lower = floor($max_tag_count - $min_tag_count / 2);
        $half_tag_point_upper = ceil($max_tag_count - $min_tag_count / 2);

        for($recordId = 1; $recordId < $records_count; $recordId++)
        {
            $tags_to_insert = $tags->random(mt_rand(
                mt_rand($min_tag_count, $half_tag_point_lower), 
                mt_rand($half_tag_point_upper, $max_tag_count)
            ));

            for($i = 0; $i < count($tags_to_insert); $i++)
            {
                $query .= '(' .
                    $recordId . ',' .
                    $tags_to_insert[$i] . ')';

                $to_insert_count++;
                $inserted_so_far++;

                if($to_insert_count == $batch_size)
                {
                    DB::insert(DB::raw($query));
    
                    $query = $query_template;
                    $to_insert_count = 0;
    
                    echo "\r" . number_format($recordId) . " records for " . number_format($inserted_so_far) . " record-tags                ";
                }
                else
                {
                    $query .= ',';
                }
            }
        }

        DB::insert(DB::raw(substr($query, 0, -1)));

        unset($query);
    
        echo "\r" . number_format($records_count) . " records for " . number_format($inserted_so_far) . " record-tags                ";

        echo "\n";

        $this->addRecordTagIndices($indices);

        $service = new \App\Services\TagService;
        $batch_size = 10000;
        for($i = 1; $i < $tags->count() + $batch_size; $i += $batch_size)
        {
                $count = $service->recalculateAllRecordCounts($i, $batch_size);
                
                if($count != null)
                    echo "\r" . number_format($count) . " tag statistics calculated                         ";
        }

        echo "\n";

        $service = new \App\Services\RecordService;
        $batch_size = 10000;
        for($i = 1; $i < $records_count + $batch_size; $i += $batch_size)
        {
            $count = $service->recalculateAllTagCounts($i, $batch_size);

            if($count != null)
                echo "\r" . number_format($count) . " record statistics calculated                         ";
        }

        $this->turnOnDBVariables();
    }

    public function dropRecordTagIndices()
    {
        $index_table = DB::select('SHOW INDEX FROM record_tag');
        $indices = [];

        foreach($index_table as $index)
        {
            if($index->Key_name == 'PRIMARY')
            {
                continue;
            }

            $indices[$index->Key_name][] = $index;
        }

        foreach($indices as $key=>$value)
        {
            //DB::statement('DROP INDEX `' . $key . '` ON record_tag');
        }

        return $indices;
    }

    public function addRecordTagIndices(array $indices)
    {
        foreach($indices as $name=>$columns)
        {
            $is_unique = $columns[0]->Non_unique == 0 ? 'UNIQUE' : '';

            $column_names = implode(',', collect($columns)->sortBy('Seq_in_index')->map(function($item){
                return $item->Column_name;
            })->toArray());

            //DB::statement("CREATE $is_unique INDEX $name ON record_tag ($column_names)");
        }
    }
}

