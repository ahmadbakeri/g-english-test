<?php
$files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator('app/Filament/Resources'));
foreach ($files as $file) {
    if ($file->isFile() && str_ends_with($file->getFilename(), 'Resource.php')) {
        $path = $file->getPathname();
        $content = file_get_contents($path);
        
        // Extract the resource name (e.g., TutorResource -> Tutor)
        preg_match('/class ([A-Za-z]+)Resource extends/', $content, $matches);
        if (!$matches) continue;
        
        $modelName = $matches[1];
        
        // Convert PascalCase to Space Case (e.g., ClassRoom -> Class Room)
        $label = preg_replace('/(?<!^)([A-Z])/', ' $1', $modelName);
        $pluralLabel = $label . 's';
        
        // Special plurals
        if (str_ends_with($pluralLabel, 'ys')) {
            $pluralLabel = substr($pluralLabel, 0, -2) . 'ies'; // Salary -> Salaries
        }
        if ($modelName === 'Attendance') $pluralLabel = 'Attendances';
        if ($modelName === 'ClassRoom') { $label = 'Class Room'; $pluralLabel = 'Class Rooms'; }
        if ($modelName === 'Honor') { $label = 'Honor'; $pluralLabel = 'Honors'; }
        
        // Avoid duplicate insertion
        if (!str_contains($content, 'public static function getModelLabel')) {
            // Find the class opening bracket
            $replacement = "{\n    public static function getModelLabel(): string\n    {\n        return __('{$label}');\n    }\n\n    public static function getPluralModelLabel(): string\n    {\n        return __('{$pluralLabel}');\n    }\n";
            $content = preg_replace('/class [A-Za-z]+Resource extends [A-Za-z]+\s*\{/', "$0\n    public static function getModelLabel(): string\n    {\n        return __('{$label}');\n    }\n\n    public static function getPluralModelLabel(): string\n    {\n        return __('{$pluralLabel}');\n    }\n", $content, 1);
            file_put_contents($path, $content);
            echo "Added labels to $modelName ($label, $pluralLabel)\n";
        }
    }
}
