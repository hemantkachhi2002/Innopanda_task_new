<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Starting Token Patch...\n";

try {
    if (!Schema::hasTable('personal_access_tokens')) {
        Schema::create('personal_access_tokens', function(Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
        });
        echo "SUCCESS: Created personal_access_tokens table.\n";
    } else {
        echo "SKIP: personal_access_tokens table already exists.\n";
    }
} catch (Exception $e) {
    echo "ERROR (tokens): " . $e->getMessage() . "\n";
}
