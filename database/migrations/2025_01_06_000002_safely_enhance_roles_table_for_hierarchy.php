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
        Schema::table('roles', function (Blueprint $table) {
            // Only add columns that don't exist
            $this->addColumnIfNotExists($table, 'level', function($table) {
                $table->integer('level')->default(0)->comment('Role hierarchy level (0=lowest, higher number=more authority)');
            });
            
            $this->addColumnIfNotExists($table, 'color', function($table) {
                $table->string('color', 7)->nullable()->comment('Hex color code for UI representation');
            });
            
            $this->addColumnIfNotExists($table, 'icon', function($table) {
                $table->string('icon')->nullable()->comment('Icon class for UI representation');
            });
            
            $this->addColumnIfNotExists($table, 'max_users', function($table) {
                $table->integer('max_users')->nullable()->comment('Maximum number of users that can have this role');
            });
            
            $this->addColumnIfNotExists($table, 'auto_assign_conditions', function($table) {
                $table->json('auto_assign_conditions')->nullable()->comment('Conditions for automatic role assignment');
            });
            
            $this->addColumnIfNotExists($table, 'restrictions', function($table) {
                $table->json('restrictions')->nullable()->comment('Additional restrictions for this role');
            });
            
            $this->addColumnIfNotExists($table, 'metadata', function($table) {
                $table->json('metadata')->nullable()->comment('Additional role metadata');
            });
            
            $this->addColumnIfNotExists($table, 'valid_from', function($table) {
                $table->timestamp('valid_from')->nullable()->comment('Role validity start date');
            });
            
            $this->addColumnIfNotExists($table, 'valid_until', function($table) {
                $table->timestamp('valid_until')->nullable()->comment('Role validity end date');
            });
            
            $this->addColumnIfNotExists($table, 'created_by', function($table) {
                $table->unsignedBigInteger('created_by')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'updated_by', function($table) {
                $table->unsignedBigInteger('updated_by')->nullable();
            });
        });

        // Add indexes if they don't exist
        $this->addIndexIfNotExists('roles', 'roles_level_index', ['level']);
        $this->addIndexIfNotExists('roles', 'roles_valid_dates_index', ['valid_from', 'valid_until']);
        
        // Add foreign key constraints if they don't exist
        $this->addForeignKeyIfNotExists('roles', 'roles_created_by_foreign', 'created_by', 'users', 'id');
        $this->addForeignKeyIfNotExists('roles', 'roles_updated_by_foreign', 'updated_by', 'users', 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table) {
            // Drop foreign keys first
            $this->dropForeignKeyIfExists('roles', 'roles_created_by_foreign');
            $this->dropForeignKeyIfExists('roles', 'roles_updated_by_foreign');
            
            // Drop columns
            $columns = [
                'level', 'color', 'icon', 'max_users', 'auto_assign_conditions',
                'restrictions', 'metadata', 'valid_from', 'valid_until',
                'created_by', 'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('roles', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addColumnIfNotExists($table, $columnName, $callback)
    {
        if (!Schema::hasColumn('roles', $columnName)) {
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
            // Index might already exist, continue
        }
    }

    private function addForeignKeyIfNotExists($tableName, $foreignKeyName, $column, $referencedTable, $referencedColumn)
    {
        try {
            if (!$this->foreignKeyExists($tableName, $foreignKeyName)) {
                Schema::table($tableName, function (Blueprint $table) use ($column, $referencedTable, $referencedColumn) {
                    $table->foreign($column)->references($referencedColumn)->on($referencedTable)->onDelete('set null');
                });
            }
        } catch (\Exception $e) {
            // Foreign key might already exist, continue
        }
    }

    private function dropForeignKeyIfExists($tableName, $foreignKeyName)
    {
        try {
            if ($this->foreignKeyExists($tableName, $foreignKeyName)) {
                Schema::table($tableName, function (Blueprint $table) use ($foreignKeyName) {
                    $table->dropForeign($foreignKeyName);
                });
            }
        } catch (\Exception $e) {
            // Foreign key might not exist, continue
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

    private function foreignKeyExists($table, $foreignKey)
    {
        try {
            $connection = Schema::getConnection();
            $schemaManager = $connection->getDoctrineSchemaManager();
            $foreignKeys = $schemaManager->listTableForeignKeys($table);
            foreach ($foreignKeys as $fk) {
                if ($fk->getName() === $foreignKey) {
                    return true;
                }
            }
            return false;
        } catch (\Exception $e) {
            return false;
        }
    }
};
