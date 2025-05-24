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
        Schema::table('partners', function (Blueprint $table) {
            $table->boolean('onboarding_completed')->default(false)->after('status');
            $table->timestamp('onboarding_completed_at')->nullable()->after('onboarding_completed');
            $table->boolean('agreement_accepted')->default(false)->after('onboarding_completed_at');
            $table->timestamp('agreement_accepted_at')->nullable()->after('agreement_accepted');
            $table->string('tourism_certificate_number')->nullable()->after('agreement_accepted_at');
            $table->date('tourism_certificate_valid_until')->nullable()->after('tourism_certificate_number');
            $table->json('staff_user_ids')->nullable()->after('tourism_certificate_valid_until');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('partners', function (Blueprint $table) {
            $table->dropColumn([
                'onboarding_completed',
                'onboarding_completed_at',
                'agreement_accepted',
                'agreement_accepted_at',
                'tourism_certificate_number',
                'tourism_certificate_valid_until',
                'staff_user_ids'
            ]);
        });
    }
};
