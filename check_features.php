<?php
$d = json_decode(file_get_contents('public/assets/maps/pakistan.geojson'), true);
foreach($d['features'] as $f) {
    echo $f['id'] . ' ' . ($f['properties']['name'] ?? 'No Name') . PHP_EOL;
}
