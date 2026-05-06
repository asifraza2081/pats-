<?php

$outputFile = 'codebase.txt';
// Hard exclusions to keep the file size manageable
$excludedPaths = ['vendor', 'node_modules', 'storage', 'public', 'bootstrap/cache', '.git', '.idea', '.vscode'];
$allowedExtensions = ['php', 'js', 'vue', 'json', 'yml', 'css'];

// 1. Initialize/Clear the file
file_put_contents($outputFile, "LARAVEL CODEBASE BUNDLE - Generated: " . date('Y-m-d H:i:s') . "\n");
file_put_contents($outputFile, "Excluding: " . implode(', ', $excludedPaths) . "\n\n", FILE_APPEND);

echo "Starting bundle process...\n";

/**
 * Recursive function to build the tree and collect files
 */
function processDirectory($dir, $prefix, &$outputFile, $excludedPaths, $allowedExtensions) {
    $items = array_diff(scandir($dir), array('.', '..'));
    
    foreach ($items as $item) {
        $path = $dir . DIRECTORY_SEPARATOR . $item;
        $relativePath = ltrim($path, './');

        // Skip excluded directories and hidden files
        if (in_array($item, $excludedPaths) || $item[0] === '.') {
            continue;
        }

        if (is_dir($path)) {
            processDirectory($path, $prefix, $outputFile, $excludedPaths, $allowedExtensions);
        } else {
            // Check if file extension is relevant
            $ext = pathinfo($path, PATHINFO_EXTENSION);
            if (in_array($ext, $allowedExtensions)) {
                echo "Adding: $relativePath\n";
                
                $content = "--- START OF FILE: $relativePath ---\n";
                $content .= file_get_contents($path);
                $content .= "\n--- END OF FILE: $relativePath ---\n\n";
                
                file_put_contents($outputFile, $content, FILE_APPEND);
            }
        }
    }
}

// 2. Generate Directory Structure first (Short summary)
echo "Generating structure summary...\n";
$structure = shell_exec("find . -maxdepth 3 -not -path '*/.*' " . implode(' ', array_map(fn($d) => "-not -path './$d*'", $excludedPaths)));
file_put_contents($outputFile, "DIRECTORY STRUCTURE (TOP LEVELS):\n" . $structure . "\n" . str_repeat("=", 60) . "\n\n", FILE_APPEND);

// 3. Run the processing
processDirectory('.', '', $outputFile, $excludedPaths, $allowedExtensions);

echo "\nDone! All relevant code is now in $outputFile\n";