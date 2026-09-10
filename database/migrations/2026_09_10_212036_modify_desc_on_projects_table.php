<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE `projects` CHANGE `desc` `description` TEXT');
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE `projects` CHANGE `description` `desc` TEXT');
    }
};