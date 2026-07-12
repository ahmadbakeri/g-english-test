<?php
$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('app/Filament/Resources'));
foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Resource.php')) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        $count = substr_count($content, 'public static function getPluralModelLabel()');
        if ($count > 1) {
            echo "Fixing $path\n";
            // Remove the FIRST occurrence that my script added at the top of the class
            $content = preg_replace("/\s+public static function getModelLabel\(\): string\s+\{\s+return __\('[^']+'\);\s+\}\s+public static function getPluralModelLabel\(\): string\s+\{\s+return __\('[^']+'\);\s+\}\s+/", "\n", $content, 1);
            file_put_contents($path, $content);
        }
    }
}
