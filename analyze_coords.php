<?php
$input = json_decode(file_get_contents('public/assets/maps/pakistan.geojson'), true);

$gbCoords = [];
$ajkCoords = [];

foreach ($input['features'] as $f) {
    if ($f['id'] === 'PK.NA') {
        $gbCoords = $f['geometry']['coordinates'][0];
    }
    if ($f['id'] === 'PK.JK') {
        $ajkCoords = $f['geometry']['coordinates'][0];
    }
}

// Find points that are on the eastern side (higher X values)
// We want to trace the boundary from north to south
// GB eastern boundary roughly starts from the northernmost tip, goes SE
// AJK eastern boundary connects to GB and goes south

// Let's just output the whole rings and I can manually pick the segment
echo "GB Ring:\n";
foreach ($gbCoords as $i => $pt) {
    if ($i % 5 == 0) echo "[$pt[0], $pt[1]], ";
}
echo "\n\nAJK Ring:\n";
foreach ($ajkCoords as $i => $pt) {
    if ($i % 5 == 0) echo "[$pt[0], $pt[1]], ";
}
echo "\n";
