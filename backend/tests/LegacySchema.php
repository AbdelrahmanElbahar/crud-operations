<?php

namespace Tests;

use Illuminate\Support\Facades\DB;

/**
 * Builds the users/blogs/posts tables from database/migrations in the in-memory SQLite test DB.
 *
 * Laravel's TestCase calls setUp<TraitName>() automatically for every trait a test uses.
 */
trait LegacySchema
{
    protected function setUpLegacySchema(): void
    {
        // Safety net: if config is ever cached with MySQL settings, phpunit.xml's SQLite
        // override is ignored. Stop before touching the real database.
        if (DB::connection()->getDriverName() !== 'sqlite') {
            $this->fail('Tests must run on the in-memory SQLite database, not '.DB::connection()->getDriverName().'.');
        }

        $this->artisan('migrate');
    }
}
