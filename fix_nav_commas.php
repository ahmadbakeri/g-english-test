<?php

$files = glob('app/Providers/Filament/*.php');

foreach ($files as $file) {
    $content = file_get_contents($file);
    
    // Add a comma at the end of the ->label() lines if they don't have one and are followed by NavigationGroup::make
    $content = preg_replace("/(->label\(fn \(\): string => __\('[^']+'\)\))\s+NavigationGroup::make/i", "$1,\n                NavigationGroup::make", $content);
    
    // Also add a comma for the last item if needed (though not strictly required in PHP array, but good for consistency)
    // Actually, trailing comma on the last item in an array is valid in PHP.
    $content = preg_replace("/(->label\(fn \(\): string => __\('[^']+'\)\))\s+\]\)/i", "$1,\n            ])", $content);

    if ($content !== file_get_contents($file)) {
        file_put_contents($file, $content);
        echo "Fixed commas in $file\n";
    }
}
echo "Done.\n";
