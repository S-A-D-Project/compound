<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TestConnectionController extends Controller
{
    public function test()
    {
        try {
            // Test the connection by trying to execute a simple query
            $result = DB::connection('turso')->select('SELECT 1 as test');
            
            // Try to create a test table if it doesn't exist
            DB::connection('turso')->statement('
                CREATE TABLE IF NOT EXISTS test_connection (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    test_value TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                )
            ');
            
            // Insert a test record
            DB::connection('turso')->table('test_connection')->insert([
                'test_value' => 'Connection test successful'
            ]);
            
            // Retrieve the test record
            $testRecord = DB::connection('turso')->table('test_connection')
                ->orderBy('created_at', 'desc')
                ->first();
            
            return response()->json([
                'status' => 'success',
                'message' => 'Database connection successful',
                'test_record' => $testRecord,
                'connection_info' => [
                    'driver' => config('database.connections.turso.driver'),
                    'url' => config('database.connections.turso.url'),
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Database connection test failed: ' . $e->getMessage());
            
            return response()->json([
                'status' => 'error',
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
