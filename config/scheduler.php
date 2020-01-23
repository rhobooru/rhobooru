<?php

return [

    /*
     * This block contains settings for various secondary data recalculations.
     * Generally, this will refresh the `cache_` columns in the data tables with
     * manual relationship counts.
     *
     * Specific `cache_` columns should be updated in a very resource-friendly manner
     * whenever normal things happen in the site (eg. a tag gets removed from a record).
     * However, if something unexpected happens (eg. bugs, you manually messing with
     * the database, etc.), these counts could drift from reality. To correct those errors,
     * background jobs will run to do the slower but more accurate method of populating these
     * columns. These settings drive those background jobs.
     *
     * Settings here should be low enough that you won't notice them. If you are starved
     * for resources or simply trust the data integrity enough, you can even disable them.
     *
     * Setting these too high can lead to timeouts, server strain, and database crashes.
     */
    'statistics' => [

        'tags' => [

            /*
             * The number of tags to recalculate statistics for during each run.
             */
            'tags_per_update' => env('tags_per_update', 100),

        ],

        'records' => [

            /*
             * The number of records to recalculate statistics for during each run.
             */
            'records_per_update' => env('records_per_update', 100),

        ],

    ],

];
