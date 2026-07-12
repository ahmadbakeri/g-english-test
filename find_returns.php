<?php
$dir = __DIR__ . '/app/Filament';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
        
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            $lineNum = $i + 1;
            
            // Look for return 'String'; without __() inside specific functions
            if (preg_match('/return\s+[\'"]([^\'"]+)[\'"]\s*;/', $line, $matches)) {
                if (!str_contains($line, '__(') && preg_match('/[A-Za-z]/', $matches[1])) {
                    // Check if it's inside getNavigationLabel, etc (heuristic: we just list them all)
                    echo "$relativePath:$lineNum: " . trim($line) . "\n";
                }
            }
            
            // Also check ->title('...')
            if (preg_match('/->title\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $line, $matches)) {
                if (!str_contains($line, '__(')) {
                    echo "$relativePath:$lineNum: " . trim($line) . "\n";
                }
            }
        }
    }
}
?>
