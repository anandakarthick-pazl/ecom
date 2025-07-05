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
        Schema::table('permissions', function (Blueprint $table) {
            // Only add columns that don't exist
            $this->addColumnIfNotExists($table, 'risk_level', function($table) {
                $table->enum('risk_level', ['low', 'medium', 'high', 'critical'])->default('low');
            });
            
            $this->addColumnIfNotExists($table, 'category', function($table) {
                $table->string('category')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'dependencies', function($table) {
                $table->json('dependencies')->nullable()->comment('Other permissions this one depends on');
            });
            
            $this->addColumnIfNotExists($table, 'conflicts_with', function($table) {
                $table->json('conflicts_with')->nullable()->comment('Permissions that conflict with this one');
            });
            
            $this->addColumnIfNotExists($table, 'conditions', function($table) {
                $table->json('conditions')->nullable()->comment('Conditions for this permission to be effective');
            });
            
            $this->addColumnIfNotExists($table, 'metadata', function($table) {
                $table->json('metadata')->nullable()->comment('Additional metadata like UI labels, tooltips, etc.');
            });
            
            $this->addColumnIfNotExists($table, 'created_by', function($table) {
                $table->unsignedBigInteger('created_by')->nullable();
            });
            
            $this->addColumnIfNotExists($table, 'updated_by', function($table) {
                $table->unsignedBigInteger('updated_by')->nullable();
            });
        });

        // Add indexes if they don't exist
        $this->addIndexIfNotExists('permissions', 'permissions_risk_level_index', ['risk_level']);
        $this->addIndexIfNotExists('permissions', 'permissions_category_index', ['category']);
        $this->addIndexIfNotExists('permissions', 'permissions_module_action_index', ['module', 'action']);
        
        // Add foreign key constraints if they don't exist
        $this->addForeignKeyIfNotExists('permissions', 'permissions_created_by_foreign', 'created_by', 'users', 'id');
        $this->addForeignKeyIfNotExists('permissions', 'permissions_updated_by_foreign', 'updated_by', 'users', 'id');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('permissions', function (Blueprint $table) {
            // Drop foreign keys first
            $this->dropForeignKeyIfExists('permissions', 'permissions_created_by_foreign');
            $this->dropForeignKeyIfExists('permissions', 'permissions_updated_by_foreign');
            
            // Drop columns
            $columns = [
                'risk_level', 'category', 'dependencies', 'conflicts_with',
                'conditions', 'metadata', 'created_by', 'updated_by'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('permissions', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }

    private function addColumnIfNotExists($table, $columnName, $callback)
    {
        if (!Schema::hasColumn('permissions', $columnName)) {
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
