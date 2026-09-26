<?php

use App\Models\Conference;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Conference::query()
            ->whereNull('slug')
            ->orderBy('id')
            ->chunkById(100, function ($conferences) {
                foreach ($conferences as $conference) {
                    $conference->slug = Conference::generateUniqueSlug(
                        $conference->title,
                        $conference->id
                    );

                    $conference->saveQuietly();
                }
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
