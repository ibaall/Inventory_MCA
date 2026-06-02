<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Mail;

try {
    echo "Sending test mail...\n";
    Mail::raw("Test Email from Laravel CLI", function($message) {
        $message->to("buatdownloadfilmm@gmail.com")->subject("Test Email");
    });
    echo "Success!\n";
} catch (\Throwable $e) {
    echo "Failed: " . $e->getMessage() . "\n";
}
