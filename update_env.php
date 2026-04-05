<?php
$envFile = '.env';
if (!file_exists($envFile)) {
    die(".env file not found");
}

$content = file_get_contents($envFile);

// Replacements for SQLite (No server required)
$replacements = [
    '/^DB_CONNECTION=.*$/m' => 'DB_CONNECTION=sqlite',
    '/^#?DB_HOST=.*$/m' => '#DB_HOST=127.0.0.1',
    '/^#?DB_PORT=.*$/m' => '#DB_PORT=3306',
    '/^#?DB_DATABASE=.*$/m' => '#DB_DATABASE=smartgate',
    '/^#?DB_USERNAME=.*$/m' => '#DB_USERNAME=root',
    '/^#?DB_PASSWORD=.*$/m' => '#DB_PASSWORD=',
];

foreach ($replacements as $pattern => $replacement) {
    if (preg_match($pattern, $content)) {
        $content = preg_replace($pattern, $replacement, $content);
    }
}

// Ensure database file exists
$dbPath = __DIR__ . '/database/database.sqlite';
if (!file_exists($dbPath)) {
    touch($dbPath);
    echo "Created database/database.sqlite\n";
}

file_put_contents($envFile, $content);
echo ".env updated successfully to use SQLite";
?>
