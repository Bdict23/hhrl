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
                $table->string('company_name', 100);
                $table->string('company_code',50)->nullable();
                $table->string('company_tin',50)->nullable();
                $table->string('company_type',80)->nullable();
                $table->string('company_description', 255)->nullable();
                $table->enum('company_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            });
        }

        if (!Schema::hasTable('branches')) {


            Schema::create('branches', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->string('branch_name',100);
                $table->string('branch_address',150)->nullable();
                $table->string('branch_code',50)->nullable();
                $table->string('branch_type',100)->nullable();
                $table->foreignId('company_id')->nullable()->constrained('companies')->onDelete('no action')->onUpdate('no action');
                $table->string('branch_email',80)->nullable();
                $table->string('branch_cell',25);
                $table->enum('branch_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');

            });}

            if (!Schema::hasTable('departments')) {

                Schema::create('departments', function (Blueprint $table) {
                    $table->id();
                    $table->string('department_name');
                    $table->string('department_description')->nullable();
                    $table->enum('department_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
                    $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action')->nullable();
                    $table->foreignId('company_id')->constrained('companies')->onDelete('no action')->onUpdate('no action')->nullable();
                    $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                    $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change


                });
            }
            if (!Schema::hasTable('employee_positions')) {

                Schema::create('employee_positions', function (Blueprint $table) {
                    $table->id();
                    $table->string('position_name', 100);
                    $table->string('position_description', 255)->nullable();
                    $table->enum('position_status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');
                    $table->timestamps();
                });

                DB::table('employee_positions')->insert([
                    [
                        'position_name' => 'Developer',
                        'position_description' => 'Responsible for developing and maintaining software applications.',
                        'position_status' => 'ACTIVE',
                        'created_at' => now(),
                        'updated_at' => now(),
                    ],
                ]);
            }

        if (!Schema::hasTable('employees')) {


            Schema::create('employees', function (Blueprint $table) {
                $table->id(); // PK, INT(11), not null, unique
                $table->string('corporate_id', 50)->nullable(); // VARCHAR(50)
                $table->string('name', 50); // VARCHAR(50)
                $table->string('middle_name', 255)->nullable(); // VARCHAR(255)
                $table->string('last_name', 255); // VARCHAR(255)
                $table->string('contact_number', 255)->nullable(); // VARCHAR(255)
                $table->foreignId('position_id')->nullable()->constrained('employee_positions')->onDelete('no action')->onUpdate('no action');
                $table->string('religion', 255)->nullable(); // VARCHAR(255)
                $table->date('birth_date')->nullable(); // DATE
                $table->foreignId('branch_id')->nullable()->constrained('branches')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('department_id')->nullable()->constrained('departments')->onDelete('no action')->onUpdate('no action');
                $table->enum('status', ['ACTIVE', 'INACTIVE'])->default('ACTIVE');


                // Foreign key (optional)
                // $table->foreign('branch_id')->references('id')->on('branches');

                $table->timestamps(); // created_at and updated_at
            });}

            // Step 5: Create an Audit Table for Tracking Created By
            Schema::create('company_audits', function (Blueprint $table) {
                $table->id();
                $table->foreignId('company_id')->constrained('companies')->onDelete('cascade')->onUpdate('cascade');
                $table->foreignId('created_by')->constrained('employees')->onDelete('cascade')->onUpdate('cascade');
                $table->timestamps();
            });

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

        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->string('module_name')->unique();
            $table->string('module_description')->nullable();
            $table->boolean('has_signatory')->default(false);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change

        });
        DB::table('modules')->insert([
            ['module_name' => 'Purchase Order', 'module_description' => 'Description for Module 1'],
            ['module_name' => 'Item Withdrawal', 'module_description' => 'Description for Module 2'],
        ]);

        
        Schema::create('module_permissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->foreignId('module_id')->constrained('modules')->onDelete('cascade');
            $table->boolean('read_only')->default(false);
            $table->boolean('full_access')->default(false);
            $table->boolean('restrict')->default(false);
            $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
            $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
        });

        
        if (!Schema::hasTable('signatories')) {
            Schema::create('signatories', function (Blueprint $table) {
                $table->id();
                $table->foreignId('employee_id')->constrained('employees')->onDelete('no action')->onUpdate('no action');
                $table->ENUM('status', ['ACTIVE', 'INACTIVE'])->notNullable()->default('active');
                $table->string('signatory_type');
                $table->foreignId('module_id')->constrained('modules')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('company_id')->constrained('companies')->onDelete('no action')->onUpdate('no action');
                $table->foreignId('branch_id')->constrained('branches')->onDelete('no action')->onUpdate('no action');
                $table->timestamp('created_at')->useCurrent(); // Set default value to current timestamp
                $table->timestamp('updated_at')->useCurrent()->useCurrentOnUpdate(); // Set default value to current timestamp and update on change
    
            });
        }


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('modules');
        Schema::dropIfExists('module_permissions');
        Schema::dropIfExists('signatories');
        Schema::dropIfExists('companies');
        Schema::dropIfExists('branches');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('employee_positions');
        Schema::dropIfExists('employees');
        Schema::dropIfExists('company_audits');
    }
};
