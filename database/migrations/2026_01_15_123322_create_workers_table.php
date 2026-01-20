<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Only create the table if it doesn't already exist
        if (!Schema::hasTable('workers')) {
            Schema::create('workers', function (Blueprint $table) {
                $table->id();

                // Agency relation
                $table->foreignId('agency_id')
                    ->constrained()
                    ->cascadeOnDelete();

                // Personal details
                $table->string('surname')->nullable();
                $table->string('forename');
                $table->string('title')->nullable();
                $table->date('date_of_birth')->nullable();
                $table->string('nationality')->nullable();

                // Contact
                $table->string('email')->unique();
                $table->string('mobile_phone')->unique();
                $table->string('home_phone')->nullable();

                // Address
                $table->string('address1')->nullable();
                $table->string('address2')->nullable();
                $table->string('city')->nullable();
                $table->string('county')->nullable();
                $table->string('postcode')->nullable();
                $table->string('country')->nullable();

                // Compliance
                $table->string('ni_number')->unique();

                // Banking
                $table->string('account_no')->nullable();
                $table->string('sort_code')->nullable();
                $table->string('bank_name')->nullable();
                $table->string('branch')->nullable();
                $table->string('bs_ref')->nullable();

                // Employment
                $table->string('job_title')->nullable();
                $table->string('end_client')->nullable();
                $table->date('start_date')->nullable();

                // External / integrations
                $table->string('sharecode')->nullable();
                $table->string('external_id')->nullable();
                $table->string('signify')->nullable();
                $table->string('venatu')->nullable();

                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('workers');
    }
};
