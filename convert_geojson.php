<?php
/**
 * Generate SVG map data from Highcharts GeoJSON + add IIOJK territory.
 * Merges KP and FATA. Fixes self-intersecting IIOJK polygon.
 */

$input = json_decode(file_get_contents('public/assets/maps/pakistan.geojson'), true);

$gb_segment = [
    [9851,8420], [9836,8320], [9780,8263], [9688,8264], [9670,8174], 
    [9615,8112], [9551,8090], [9499,8112], [9450,8102], [9363,8043], 
    [9273,8018], [9208,8026], [9178,7981], [9056,7878], [8948,7867], 
    [8772,7902], [8687,7961]
];

$ajk_segment = [
    [8687,7961], [8552,7946], [8319,7979], [8150,8030], [8068,8029], 
    [7963,7962], [7860,7939], [7833,7917], [7841,7856], [7801,7816], 
    [7792,7768], [7749,7698], [7770,7664], [7859,7650], [7902,7567], 
    [7832,7475], [7875,7418], [7940,7428], [7989,7417], [8061,7429], 
    [8092,7391], [8073,7341], [8011,7293], [7941,7277], [7905,7220], 
    [7909,7133], [8022,7060], [8035,7017], [8006,6897], [7940,6834], 
    [7944,6761], [8050,6658], [8138,6637], [8165,6609], [8179,6529], 
    [8168,6491]
];

// Outer boundary of IIOJK (eastern side)
// From south (Jammu) [8168,6491] looping east and north to Aksai Chin [9851,8420]
$outer_segment = [
    [8168,6491], 
    [8300,6400], [8450,6350], [8600,6320], [8800,6350], 
    [9000,6450], [9200,6600], [9400,6800], [9600,7050], 
    [9800,7350], [10000,7650], [10200,7950], [10100,8250], 
    [9950,8380], [9851,8420]
];

// Do not reverse the outer segment - we want it to go south-to-north!
$iiojk_coords = array_merge($gb_segment, $ajk_segment, $outer_segment);

$iiojk = [
    'type' => 'Feature',
    'id' => 'PK.II',
    'properties' => [
        'name' => 'IIOJK',
        'hc-key' => 'pk-ii',
    ],
    'geometry' => [
        'type' => 'Polygon',
        'coordinates' => [$iiojk_coords]
    ]
];

$input['features'][] = $iiojk;

$nameMap = [
    'Sind'            => ['name' => 'Sindh',                      'code' => 'PK-SD'],
    'Baluchistan'     => ['name' => 'Balochistan',                'code' => 'PK-BA'],
    'Azad Kashmir'    => ['name' => 'Azad Jammu & Kashmir',       'code' => 'PK-JK'],
    'Northern Areas'  => ['name' => 'Gilgit-Baltistan',           'code' => 'PK-GB'],
    'N.W.F.P.'        => ['name' => 'Khyber Pakhtunkhwa',         'code' => 'PK-KP'],
    'F.A.T.A.'        => ['name' => 'Khyber Pakhtunkhwa',         'code' => 'PK-KP'], // Merge into KP
    'F.C.T.'          => ['name' => 'Islamabad Capital Territory','code' => 'PK-IS'],
    'Punjab'          => ['name' => 'Punjab',                     'code' => 'PK-PB'],
    'IIOJK'           => ['name' => 'IIOJK (Indian Illegally Occupied J&K)', 'code' => 'PK-II'],
];

// Find bounding box
$minX = PHP_INT_MAX; $minY = PHP_INT_MAX;
$maxX = PHP_INT_MIN; $maxY = PHP_INT_MIN;
foreach ($input['features'] as $feature) {
    foreach ($feature['geometry']['coordinates'] as $ring) {
        foreach ($ring as $point) {
            $minX = min($minX, $point[0]);
            $maxX = max($maxX, $point[0]);
            $minY = min($minY, $point[1]);
            $maxY = max($maxY, $point[1]);
        }
    }
}

$svgWidth = 900;
$svgHeight = 900;
$dataWidth = $maxX - $minX;
$dataHeight = $maxY - $minY;
$padding = 20;
$scaleX = ($svgWidth - 2 * $padding) / $dataWidth;
$scaleY = ($svgHeight - 2 * $padding) / $dataHeight;
$scale = min($scaleX, $scaleY);
$offsetX = $padding + (($svgWidth - 2*$padding) - $dataWidth * $scale) / 2;
$offsetY = $padding + (($svgHeight - 2*$padding) - $dataHeight * $scale) / 2;

function transformPoint($x, $y) {
    global $minX, $maxY, $scale, $offsetX, $offsetY;
    $sx = ($x - $minX) * $scale + $offsetX;
    $sy = ($maxY - $y) * $scale + $offsetY;
    return [round($sx, 1), round($sy, 1)];
}

$provincesData = [];

foreach ($input['features'] as $feature) {
    $oldName = $feature['properties']['name'];
    $mapped = $nameMap[$oldName] ?? ['name' => $oldName, 'code' => $feature['id'] ?? 'PK-XX'];
    $code = $mapped['code'];
    
    $pathParts = [];
    foreach ($feature['geometry']['coordinates'] as $ring) {
        $points = [];
        foreach ($ring as $i => $point) {
            [$sx, $sy] = transformPoint($point[0], $point[1]);
            $cmd = ($i === 0) ? 'M' : 'L';
            $points[] = "{$cmd}{$sx},{$sy}";
        }
        $pathParts[] = implode('', $points) . 'Z';
    }
    
    $cx = 0; $cy = 0; $n = 0;
    foreach ($feature['geometry']['coordinates'][0] as $point) {
        [$sx, $sy] = transformPoint($point[0], $point[1]);
        $cx += $sx; $cy += $sy; $n++;
    }
    
    if (!isset($provincesData[$code])) {
        $provincesData[$code] = [
            'code' => $code,
            'name' => $mapped['name'],
            'path' => '',
            'cx' => 0,
            'cy' => 0,
            'pts' => 0,
            'disputed' => ($code === 'PK-II')
        ];
    }
    
    // Append path (this effectively merges KP and FATA paths into one compound path)
    $provincesData[$code]['path'] .= implode('', $pathParts);
    $provincesData[$code]['cx'] += $cx * $n; // weighted average
    $provincesData[$code]['cy'] += $cy * $n;
    $provincesData[$code]['pts'] += $n;
}

// Finalize
$provinces = [];
foreach ($provincesData as $p) {
    $provinces[] = [
        'code' => $p['code'],
        'name' => $p['name'],
        'path' => $p['path'],
        'cx' => round($p['cx'] / $p['pts'], 1),
        'cy' => round($p['cy'] / $p['pts'], 1),
        'disputed' => $p['disputed']
    ];
    echo "{$p['code']} ({$p['name']})\n";
}

file_put_contents('public/assets/maps/pakistan_svg_data.json', json_encode($provinces, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "\nDone! ViewBox: 0 0 {$svgWidth} {$svgHeight}\n";
