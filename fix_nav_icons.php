<?php

$files = glob('app/Providers/Filament/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    // Use regex to remove any line containing ->icon('heroicon-o-...') when it follows NavigationGroup::make
    // Wait, the safest way is just to remove all lines that match ->icon('...') inside the navigationGroups array.
    // Let's just remove lines that match /->icon\('heroicon-o-[^']+'\),?/ that come right after a label.
    
    // Pattern: ->icon('heroicon-o-something') followed optionally by a comma
    $newContent = preg_replace("/\s*->icon\('[^']+'\),?/", "", $content);
    
    // Ensure we only affect the NavigationGroup icons by being more specific if needed, 
    // but looking at the file, the only place where ->icon('...') is on a line by itself like that
    // inside the PanelProvider might be those groups. Let's do a more robust string replacement.
    
    $replacements = [
        "->icon('heroicon-o-building-office')," => "",
        "->icon('heroicon-o-academic-cap')," => "",
        "->icon('heroicon-o-clipboard-document-check')," => "",
        "->icon('heroicon-o-banknotes')," => "",
        "->icon('heroicon-o-chart-bar')," => "",
        "->icon('heroicon-o-cog-6-tooth')," => "",
        "->icon('heroicon-o-cog')," => "",
        // some might not have comma at the end
        "->icon('heroicon-o-cog')" => "",
    ];
    
    foreach ($replacements as $search => $replace) {
        $content = str_replace($search, $replace, $content);
    }
    
    // Clean up empty lines that might have been left
    $content = preg_replace("/\n\s*\n/", "\n", $content);
    
    if ($content !== file_get_contents($file)) {
        file_put_contents($file, $content);
        echo "Updated $file\n";
    }
}
echo "Done.\n";
