<?php

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
        Schema::table('government_testimonials', function (Blueprint $table) {
            if (!Schema::hasColumn('government_testimonials', 'position')) {
                $table->string('position')->nullable()->after('title');
            }
            
            if (!Schema::hasColumn('government_testimonials', 'is_featured')) {
                $table->boolean('is_featured')->default(false)->after('status');
            }
            
            if (!Schema::hasColumn('government_testimonials', 'service_id')) {
                $table->foreignId('service_id')->nullable()->after('is_featured')
                    ->constrained('government_services')->nullOnDelete();
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('government_testimonials', function (Blueprint $table) {
            if (Schema::hasColumn('government_testimonials', 'position')) {
                $table->dropColumn('position');
            }
            
            if (Schema::hasColumn('government_testimonials', 'is_featured')) {
                $table->dropColumn('is_featured');
            }
            
            if (Schema::hasColumn('government_testimonials', 'service_id')) {
                $table->dropForeign(['service_id']);
                $table->dropColumn('service_id');
            }
        });
    }
};
