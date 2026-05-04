<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Starting Manual Patch...\n";

try {
    if (!Schema::hasColumn('users', 'role')) {
        Schema::table('users', function(Blueprint $table) {
            $table->enum('role', ['admin', 'user'])->default('user');
        });
        echo "SUCCESS: Added 'role' column to users.\n";
    } else {
        echo "SKIP: 'role' column already exists in users.\n";
    }
} catch (Exception $e) {
    echo "ERROR (users): " . $e->getMessage() . "\n";
}

try {
    if (!Schema::hasTable('resources')) {
        Schema::create('resources', function(Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->timestamps();
        });
        echo "SUCCESS: Created resources table.\n";
    } else {
        echo "SKIP: resources table already exists.\n";
    }
} catch (Exception $e) {
    echo "ERROR (resources): " . $e->getMessage() . "\n";
}

try {
    if (!Schema::hasTable('bookings')) {
        Schema::create('bookings', function(Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('resource_id')->constrained()->onDelete('cascade');
            $table->date('date');
            $table->time('start_time');
            $table->time('end_time');
            $table->timestamps();
            $table->index(['resource_id', 'date']);
        });
        echo "SUCCESS: Created bookings table.\n";
    } else {
        echo "SKIP: bookings table already exists.\n";
    }
} catch (Exception $e) {
    echo "ERROR (bookings): " . $e->getMessage() . "\n";
}
