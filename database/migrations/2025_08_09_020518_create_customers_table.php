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
        Schema::create('customers', function (Blueprint $table) {
            // The `dob` column was updated to `date_of_birth` 
            // The migration was needed to be rolled back
            // which is not proper especially when you already have
            // plenty of tables
            // You must add a new migration file when updating column names or any table properties
            // eg. `php artisan make:migration update_dob_column_name_to_date_of_birth_from_customers_table --table=customers`
            // so you don't need to rollback the migration which is risky when not properly handled


            $table->id();
            $table->string('name');                // required
            $table->date('date_of_birth');         // required
            $table->string('address')->nullable();
            $table->string('email')->unique();     // required
            $table->string('contact');             // required
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
