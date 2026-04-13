<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $indexExists = collect(DB::select("SHOW INDEX FROM lawyer_blocked_dates WHERE Key_name = 'lawyer_blocked_dates_lawyer_id_index'"))->isNotEmpty();
        if (!$indexExists) {
            Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
                $table->index('lawyer_id', 'lawyer_blocked_dates_lawyer_id_index');
            });
        }

        Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
            if (!Schema::hasColumn('lawyer_blocked_dates', 'start_time')) {
                $table->time('start_time')->nullable()->after('blocked_date');
            }
            if (!Schema::hasColumn('lawyer_blocked_dates', 'end_time')) {
                $table->time('end_time')->nullable()->after('start_time');
            }
        });

        $uniqueExists = collect(DB::select("SHOW INDEX FROM lawyer_blocked_dates WHERE Key_name = 'lawyer_blocked_dates_lawyer_id_blocked_date_unique'"))->isNotEmpty();
        if ($uniqueExists) {
            Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
                $table->dropUnique('lawyer_blocked_dates_lawyer_id_blocked_date_unique');
            });
        }
    }

    public function down(): void
    {
        $hasUnique = collect(DB::select("SHOW INDEX FROM lawyer_blocked_dates WHERE Key_name = 'lawyer_blocked_dates_lawyer_id_blocked_date_unique'"))->isNotEmpty();
        if (!$hasUnique) {
            Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
                $table->unique(['lawyer_id', 'blocked_date']);
            });
        }

        Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
            if (Schema::hasColumn('lawyer_blocked_dates', 'start_time')) {
                $table->dropColumn('start_time');
            }
            if (Schema::hasColumn('lawyer_blocked_dates', 'end_time')) {
                $table->dropColumn('end_time');
            }
        });

        $plainIndexExists = collect(DB::select("SHOW INDEX FROM lawyer_blocked_dates WHERE Key_name = 'lawyer_blocked_dates_lawyer_id_index'"))->isNotEmpty();
        if ($plainIndexExists) {
            Schema::table('lawyer_blocked_dates', function (Blueprint $table) {
                $table->dropIndex('lawyer_blocked_dates_lawyer_id_index');
            });
        }
    }
};
