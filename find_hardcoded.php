<?php
$dir = __DIR__ . '/app/Filament';
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
$found = [];

// Patterns to look for:
// ->label('text') or ->label("text")
// Section::make('text')
// return 'text'; inside getNavigationLabel, getModelLabel, getPluralModelLabel, getNavigationGroup
// ->title('text')

foreach ($iterator as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $content = file_get_contents($file->getPathname());
        $relativePath = str_replace(__DIR__ . '/', '', $file->getPathname());
        
        $lines = explode("\n", $content);
        foreach ($lines as $i => $line) {
            $lineNum = $i + 1;
            
            // Check label('...') without __()
            if (preg_match('/->label\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $line, $matches)) {
                // If the matched text doesn't look like a technical key (e.g. contains spaces, uppercase)
                // and is not wrapped in __()
                if (!str_contains($line, '__(')) {
                    $found[] = "$relativePath:$lineNum: " . trim($line);
                }
            }
            // Check Section::make('...') without __()
            if (preg_match('/Section::make\(\s*[\'"]([^\'"]+)[\'"]\s*\)/', $line, $matches)) {
                if (!str_contains($line, '__(')) {
                    $found[] = "$relativePath:$lineNum: " . trim($line);
                }
            }
            
            // Check return 'Text'; inside Filament typical translation methods
            // (a bit harder with regex, but we can look for return 'Text'; without __() and starting with capital letter)
            if (preg_match('/return\s+[\'"]([A-Z][^\'"]+)[\'"]\s*;/', $line, $matches)) {
                if (!str_contains($line, '__(')) {
                    $found[] = "$relativePath:$lineNum: " . trim($line);
                }
            }
        }
    }
}

echo "Found " . count($found) . " potential hardcoded strings:\n";
foreach ($found as $f) {
    echo $f . "\n";
}
?>
