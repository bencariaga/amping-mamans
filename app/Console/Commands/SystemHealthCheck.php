<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

class SystemHealthCheck extends Command
{
    protected $signature = 'system:health-check';
    protected $description = 'Run a comprehensive system health check';

    private $issues = [];
    private $optimizations = [];
    private $status = 'Working';

    public function handle()
    {
        $this->info('🔍 Starting System Health Check...');
        $this->newLine();

        // 1. Check Database Connection
        $this->checkDatabase();
        
        // 2. Check File System
        $this->checkFileSystem();
        
        // 3. Check Configuration
        $this->checkConfiguration();
        
        // 4. Check Dependencies
        $this->checkDependencies();
        
        // 5. Check Performance
        $this->checkPerformance();
        
        // 6. Generate Report
        $this->generateReport();
    }

    private function checkDatabase()
    {
        $this->info('📊 Checking Database...');
        
        try {
            DB::connection()->getPdo();
            $this->info('✅ Database connection successful');
            
            // Check tables
            $tables = DB::select('SHOW TABLES');
            $tableCount = count($tables);
            $this->info("✅ Found {$tableCount} database tables");
            
            // Check for orphaned records
            $orphanedPatients = DB::table('patients')
                ->leftJoin('applicants', 'patients.applicant_id', '=', 'applicants.applicant_id')
                ->whereNull('applicants.applicant_id')
                ->count();
            
            if ($orphanedPatients > 0) {
                $this->issues[] = "Found {$orphanedPatients} orphaned patient records";
            }
            
        } catch (\Exception $e) {
            $this->error('❌ Database connection failed: ' . $e->getMessage());
            $this->status = 'With Issues';
            $this->issues[] = 'Database connection error: ' . $e->getMessage();
        }
    }

    private function checkFileSystem()
    {
        $this->info('📁 Checking File System...');
        
        // Check storage link
        if (File::exists(public_path('storage'))) {
            $this->info('✅ Storage link exists');
        } else {
            $this->warn('⚠️ Storage link missing');
            $this->issues[] = 'Storage link missing - run: php artisan storage:link';
        }
        
        // Check writable directories
        $writableDirectories = [
            storage_path('app'),
            storage_path('framework/cache'),
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('logs'),
        ];
        
        foreach ($writableDirectories as $dir) {
            if (!is_writable($dir)) {
                $this->warn("⚠️ Directory not writable: {$dir}");
                $this->issues[] = "Directory not writable: {$dir}";
            }
        }
        
        // Check log file size
        $logFile = storage_path('logs/laravel.log');
        if (File::exists($logFile)) {
            $size = File::size($logFile);
            $sizeMB = round($size / 1024 / 1024, 2);
            
            if ($sizeMB > 10) {
                $this->warn("⚠️ Log file is large: {$sizeMB}MB");
                $this->optimizations[] = "Clear large log file ({$sizeMB}MB)";
            } else {
                $this->info("✅ Log file size: {$sizeMB}MB");
            }
        }
    }

    private function checkConfiguration()
    {
        $this->info('⚙️ Checking Configuration...');
        
        // Check cache status
        $configCached = File::exists(base_path('bootstrap/cache/config.php'));
        $routesCached = File::exists(base_path('bootstrap/cache/routes-v7.php'));
        
        if ($configCached && $routesCached) {
            $this->info('✅ Configuration and routes are cached');
        } else {
            if (!$configCached) {
                $this->optimizations[] = 'Cache configuration: php artisan config:cache';
            }
            if (!$routesCached) {
                $this->optimizations[] = 'Cache routes: php artisan route:cache';
            }
        }
        
        // Check environment
        if (config('app.debug') === true) {
            $this->warn('⚠️ Debug mode is enabled');
            $this->optimizations[] = 'Disable debug mode in production';
        }
        
        if (config('app.env') === 'local') {
            $this->info('✅ Running in local environment');
        }
    }

    private function checkDependencies()
    {
        $this->info('📦 Checking Dependencies...');
        
        // Check Composer dependencies
        if (File::exists(base_path('vendor/autoload.php'))) {
            $this->info('✅ Composer dependencies installed');
        } else {
            $this->error('❌ Composer dependencies not installed');
            $this->issues[] = 'Run: composer install';
            $this->status = 'With Issues';
        }
        
        // Check Node modules
        if (File::exists(base_path('node_modules'))) {
            $this->info('✅ Node modules installed');
        } else {
            $this->warn('⚠️ Node modules not installed');
            $this->issues[] = 'Run: npm install';
        }
        
        // Check built assets
        if (File::exists(public_path('build/manifest.json'))) {
            $this->info('✅ Assets are built');
        } else {
            $this->warn('⚠️ Assets not built');
            $this->issues[] = 'Run: npm run build';
        }
    }

    private function checkPerformance()
    {
        $this->info('⚡ Checking Performance...');
        
        // Check for N+1 queries potential
        $controllersWithoutEagerLoading = [];
        $controllerPath = app_path('Http/Controllers');
        $files = File::allFiles($controllerPath);
        
        foreach ($files as $file) {
            $content = File::get($file->getPathname());
            if (preg_match('/\->all\(\)/', $content) && !preg_match('/\->with\(/', $content)) {
                $controllersWithoutEagerLoading[] = basename($file->getFilename(), '.php');
            }
        }
        
        if (count($controllersWithoutEagerLoading) > 0) {
            $this->warn('⚠️ Potential N+1 queries in: ' . implode(', ', array_slice($controllersWithoutEagerLoading, 0, 3)));
            $this->optimizations[] = 'Review controllers for eager loading opportunities';
        }
        
        // Check query performance
        try {
            $slowQueries = DB::select("SHOW VARIABLES LIKE 'slow_query_log'");
            if ($slowQueries && $slowQueries[0]->Value === 'OFF') {
                $this->info('✅ Slow query log is off (normal for local dev)');
            }
        } catch (\Exception $e) {
            // Ignore if not supported
        }
    }

    private function generateReport()
    {
        $this->newLine();
        $this->info('=' . str_repeat('=', 60));
        $this->info('📋 SYSTEM HEALTH CHECK REPORT');
        $this->info('=' . str_repeat('=', 60));
        $this->newLine();
        
        // Overall Status
        $statusColor = $this->status === 'Working' ? 'green' : 'red';
        $statusIcon = $this->status === 'Working' ? '✅' : '⚠️';
        $this->line("<fg={$statusColor}>{$statusIcon} Overall System Status: {$this->status}</>");
        $this->newLine();
        
        // Issues
        if (count($this->issues) > 0) {
            $this->error('🔴 Issues Found (' . count($this->issues) . '):');
            foreach ($this->issues as $issue) {
                $this->line("   • {$issue}");
            }
            $this->newLine();
        } else {
            $this->info('✅ No critical issues found!');
            $this->newLine();
        }
        
        // Optimizations
        if (count($this->optimizations) > 0) {
            $this->warn('💡 Suggested Optimizations (' . count($this->optimizations) . '):');
            foreach ($this->optimizations as $optimization) {
                $this->line("   • {$optimization}");
            }
            $this->newLine();
        }
        
        // Summary
        $this->info('📊 Summary:');
        $this->table(
            ['Component', 'Status'],
            [
                ['Database', '✅ Connected'],
                ['File System', count($this->issues) > 0 ? '⚠️ Issues' : '✅ OK'],
                ['Configuration', $this->isOptimized() ? '✅ Optimized' : '⚠️ Not Optimized'],
                ['Dependencies', $this->hasDependencies() ? '✅ Installed' : '⚠️ Missing'],
                ['Performance', count($this->optimizations) > 0 ? '⚠️ Can Improve' : '✅ Good'],
            ]
        );
        
        $this->newLine();
        $this->info('✨ Health check completed!');
    }

    private function isOptimized(): bool
    {
        return File::exists(base_path('bootstrap/cache/config.php')) && 
               File::exists(base_path('bootstrap/cache/routes-v7.php'));
    }

    private function hasDependencies(): bool
    {
        return File::exists(base_path('vendor/autoload.php')) && 
               File::exists(base_path('node_modules'));
    }
}
