<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $blankTagIds = DB::table('tags')
            ->select(['id', 'name'])
            ->get()
            ->filter(function ($tag) {
                $name = (string) $tag->name;
                $name = preg_replace('/^[\s\p{Z}]+|[\s\p{Z}]+$/u', '', $name);

                return $name === '';
            })
            ->pluck('id')
            ->values()
            ->all();

        if ($blankTagIds === []) {
            return;
        }

        DB::table('live_post_tag')
            ->whereIn('tag_id', $blankTagIds)
            ->delete();

        DB::table('tags')
            ->whereIn('id', $blankTagIds)
            ->delete();
    }

    public function down(): void
    {
        //
    }
};
