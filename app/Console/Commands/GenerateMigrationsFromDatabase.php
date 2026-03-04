<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class GenerateMigrationsFromDatabase extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:migrations 
                            {--tables=* : Specific tables to generate migrations for}
                            {--ignore=* : Tables to ignore}
                            {--path= : Custom path for migrations}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate migration files from existing database tables';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('🚀 Starting migration generation from database...');
        
        // Get all tables
        $tables = $this->getTables();
        
        // Filter tables
        $specificTables = $this->option('tables');
        $ignoreTables = array_merge(
            ['migrations', 'password_resets', 'failed_jobs', 'personal_access_tokens', 'sessions'],
            $this->option('ignore')
        );
        
        if (!empty($specificTables)) {
            $tables = array_intersect($tables, $specificTables);
        }
        
        $tables = array_diff($tables, $ignoreTables);
        
        $this->info('📊 Found ' . count($tables) . ' tables to process');
        
        $path = $this->option('path') ?: database_path('migrations');
        
        $progressBar = $this->output->createProgressBar(count($tables));
        $progressBar->start();
        
        foreach ($tables as $table) {
            try {
                $this->generateMigration($table, $path);
                $progressBar->advance();
            } catch (\Exception $e) {
                $this->error("\n❌ Error generating migration for {$table}: " . $e->getMessage());
            }
        }
        
        $progressBar->finish();
        $this->info("\n\n✅ Migration generation complete!");
        $this->info("📁 Migrations created in: {$path}");
        
        return 0;
    }
    
    /**
     * Get all tables from database
     */
    protected function getTables()
    {
        $database = env('DB_DATABASE');
        $tables = DB::select("SHOW TABLES");
        
        return array_map(function($table) use ($database) {
            $key = "Tables_in_{$database}";
            return $table->$key;
        }, $tables);
    }
    
    /**
     * Generate migration file for a table
     */
    protected function generateMigration($tableName, $path)
    {
        $className = 'Create' . Str::studly($tableName) . 'Table';
        
        // Use a simple incrementing counter for timestamp
        static $counter = 0;
        $counter++;
        $timestamp = date('Y_m_d_His', strtotime('2024-01-01 00:00:00 +' . $counter . ' seconds'));
        $filename = "{$timestamp}_create_{$tableName}_table.php";
        
        // Get table structure
        $columns = DB::select("DESCRIBE {$tableName}");
        $indexes = DB::select("SHOW INDEXES FROM {$tableName}");
        
        // Generate migration content
        $content = $this->generateMigrationContent($tableName, $className, $columns, $indexes);
        
        // Write file
        file_put_contents("{$path}/{$filename}", $content);
    }
    
    /**
     * Generate migration file content
     */
    protected function generateMigrationContent($tableName, $className, $columns, $indexes)
    {
        $columnsCode = $this->generateColumnsCode($columns);
        $indexesCode = $this->generateIndexesCode($indexes, $tableName);
        
        return <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class {$className} extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
{$columnsCode}
{$indexesCode}
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('{$tableName}');
    }
}
PHP;
    }
    
    /**
     * Generate columns code
     */
    protected function generateColumnsCode($columns)
    {
        $code = [];
        
        foreach ($columns as $column) {
            $line = $this->generateColumnDefinition($column);
            if ($line) {
                $code[] = "            {$line}";
            }
        }
        
        return implode("\n", $code);
    }
    
    /**
     * Generate single column definition
     */
    protected function generateColumnDefinition($column)
    {
        $name = $column->Field;
        $type = $column->Type;
        $null = $column->Null === 'YES';
        $key = $column->Key;
        $default = $column->Default;
        $extra = $column->Extra;
        
        // Handle auto increment
        if ($extra === 'auto_increment') {
            if ($key === 'PRI') {
                return "\$table->id('{$name}');";
            }
            return "\$table->increments('{$name}');";
        }
        
        // Parse type and length
        preg_match('/^(\w+)(?:\(([^)]+)\))?/', $type, $matches);
        $baseType = $matches[1];
        $length = $matches[2] ?? null;
        
        // Map MySQL types to Laravel
        $columnDef = $this->mapColumnType($baseType, $name, $length);
        
        if (!$columnDef) {
            $columnDef = "\$table->string('{$name}')";
        }
        
        // Add nullable
        if ($null && $key !== 'PRI') {
            $columnDef .= "->nullable()";
        }
        
        // Add default
        if ($default !== null && $default !== '' && $extra !== 'auto_increment') {
            if ($default === 'CURRENT_TIMESTAMP' || $default === 'current_timestamp()') {
                $columnDef .= "->useCurrent()";
            } else {
                $escapedDefault = addslashes($default);
                $columnDef .= "->default('{$escapedDefault}')";
            }
        }
        
        // Add comment if type has it
        if (stripos($type, 'comment') !== false) {
            // Extract comment if present
        }
        
        return $columnDef . ";";
    }
    
    /**
     * Map MySQL column types to Laravel
     */
    protected function mapColumnType($type, $name, $length)
    {
        // Handle timestamps
        if (in_array($name, ['created_at', 'updated_at'])) {
            if ($name === 'created_at') {
                return "\$table->timestamps()";
            }
            return null; // Skip updated_at as it's handled by timestamps()
        }
        
        $typeMap = [
            'int' => "integer",
            'tinyint' => "tinyInteger",
            'smallint' => "smallInteger",
            'mediumint' => "mediumInteger",
            'bigint' => "bigInteger",
            'varchar' => "string",
            'char' => "char",
            'text' => "text",
            'mediumtext' => "mediumText",
            'longtext' => "longText",
            'date' => "date",
            'datetime' => "dateTime",
            'timestamp' => "timestamp",
            'time' => "time",
            'year' => "year",
            'decimal' => "decimal",
            'float' => "float",
            'double' => "double",
            'boolean' => "boolean",
            'enum' => "enum",
            'json' => "json",
        ];
        
        if (isset($typeMap[$type])) {
            $method = $typeMap[$type];
            
            if ($type === 'decimal' && $length) {
                list($precision, $scale) = explode(',', $length);
                return "\$table->{$method}('{$name}', {$precision}, {$scale})";
            } elseif (in_array($type, ['varchar', 'char']) && $length) {
                return "\$table->{$method}('{$name}', {$length})";
            } elseif ($type === 'enum') {
                // For enum, we'd need to extract values from DESCRIBE - skip for now
                return "\$table->string('{$name}')"; // Fallback to string
            } else {
                return "\$table->{$method}('{$name}')";
            }
        }
        
        return "\$table->string('{$name}')"; // Default fallback
    }
    
    /**
     * Generate indexes code
     */
    protected function generateIndexesCode($indexes, $tableName)
    {
        $code = [];
        $processed = [];
        
        foreach ($indexes as $index) {
            $keyName = $index->Key_name;
            
            // Skip if already processed
            if (in_array($keyName, $processed)) {
                continue;
            }
            
            // Skip PRIMARY key (handled by id())
            if ($keyName === 'PRIMARY') {
                continue;
            }
            
            $processed[] = $keyName;
            
            // Get all columns for this index
            $indexColumns = array_filter($indexes, function($idx) use ($keyName) {
                return $idx->Key_name === $keyName;
            });
            
            $columns = array_map(function($idx) {
                return $idx->Column_name;
            }, $indexColumns);
            
            $columnsStr = count($columns) > 1 
                ? "['" . implode("', '", $columns) . "']"
                : "'{$columns[0]}'";
            
            // Determine index type
            if ($index->Non_unique == 0) {
                $code[] = "            \$table->unique({$columnsStr});";
            } else {
                $code[] = "            \$table->index({$columnsStr});";
            }
        }
        
        return empty($code) ? '' : "\n" . implode("\n", $code);
    }
}
