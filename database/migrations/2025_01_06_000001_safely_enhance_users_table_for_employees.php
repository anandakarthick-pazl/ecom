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
        Schema::table('users', function (Blueprint $table) {
            // Only add columns that don't exist
            $this->addColumnIfNotExists($table, 'emergency_contact_name', function($table) {
                $table->string('emergency_contact_name')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'emergency_contact_phone', function($table) {
                $table->string('emergency_contact_phone')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'address', function($table) {
                $table->text('address')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'date_of_birth', function($table) {
                $table->date('date_of_birth')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'employee_type', function($table) {
                $table->enum('employee_type', ['full_time', 'part_time', 'contract', 'intern'])->default('full_time');
            });
            
            $this->addColumnIfNotExists($table, 'onboarding_data', function($table) {
                $table->json('onboarding_data')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'performance_data', function($table) {
                $table->json('performance_data')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'training_data', function($table) {
                $table->json('training_data')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'last_review_date', function($table) {
                $table->date('last_review_date')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'next_review_date', function($table) {
                $table->date('next_review_date')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'probation_end_date', function($table) {
                $table->date('probation_end_date')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'termination_date', function($table) {
                $table->date('termination_date')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'termination_reason', function($table) {
                $table->text('termination_reason')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'notes', function($table) {
                $table->text('notes')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'two_factor_enabled', function($table) {
                $table->boolean('two_factor_enabled')->default(false);
            });
            
            $this->addColumnIfNotExists($table, 'two_factor_secret', function($table) {
                $table->string('two_factor_secret')->nullable();
            });
        });

        // Add indexes if they don't exist
        $this->addIndexIfNotExists('users', 'users_department_index', ['department']);
        $this->addIndexIfNotExists('users', 'users_employee_type_index', ['employee_type']);
        $this->addIndexIfNotExists('users', 'users_company_status_index', ['company_id', 'status']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $columns = [
                'emergency_contact_name', 'emergency_contact_phone', 'address', 
                'date_of_birth', 'employee_type', 'onboarding_data', 'performance_data',
                'training_data', 'last_review_date', 'next_review_date', 
                'probation_end_date', 'termination_date', 'termination_reason',
                'notes', 'two_factor_enabled', 'two_factor_secret'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addColumnIfNotExists($table, $columnName, $callback)
    {
        if (!Schema::hasColumn('users', $columnName)) {
            $callback($table);
        }
    }

    private function addIndexIfNotExists($tableName, $indexName, $columns)
    {
        try {
            if (!$this->indexExists($tableName, $indexName)) {
                Schema::table($tableName, function (Blueprint $table) use ($columns) {
                    $table->index($columns);
                });
            }
        } catch (\Exception $e) {
            // Index might already exist with different name, continue
        }
    }

    private function indexExists($table, $index)
    {
        try {
            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            $indexes = $schemaManager->listTableIndexes($table);
            return array_key_exists($index, $indexes);
        } catch (\Exception $e) {
            return false;
        }
    }
};
