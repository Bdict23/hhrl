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
        if (!Schema::hasTable('companies')) {


            Schema::create('companies', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('company_name');
                $table->string('company_code');
                $table->string('company_tin');
                $table->string('company_type');
                $table->string('company_description');
                $table->string('company_status')->default('active');

            });
        }

        if (!Schema::hasTable('branches')) {


            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('branch_name');
                $table->string('branch_address');
                $table->string('branch_code');
                $table->string('branch_type');
                $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
                $table->string('branch_email');
                $table->string('branch_cell');
                $table->string('branch_status')->default('active');


            });}

            if (!Schema::hasTable('departments')) {

                Schema::create('departments', function (Blueprint $table) {
                    $table->id();
                    $table->string('department_name');
                    $table->string('department_description');
                    $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                    $table->string('department_status')->default('active');
                    $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change


                });
            }

        if (!Schema::hasTable('employees')) {


            Schema::create('employees', function (Blueprint $table) {
                $table->id(); // PK, INT(11), not null, unique
                $table->string('name', 50); // VARCHAR(50)
                $table->string('middle_name', 255)->nullable(); // VARCHAR(255)
                $table->string('last_name', 255); // VARCHAR(255)
                $table->string('contact_number', 255)->nullable(); // VARCHAR(255)
                $table->string('position', 255); // VARCHAR(255)
                $table->string('religion', 255)->nullable(); // VARCHAR(255)
                $table->date('birth_date')->nullable(); // DATE
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('no action')->onUpdate('no action');


                // Foreign key (optional)
                // $table->foreign('branch_id')->references('id')->on('branches');

                $table->timestamps(); // created_at and updated_at
            });}

        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('emp_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
            $table->string('name');
            $table->foreignId('branch_id')->constrained('branches')->onDelete('cascade');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
