<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 12mm 8mm; }
  body { font-family: Arial, Helvetica, sans-serif; font-size: 10px; color: #000; margin: 0; padding: 0; }

  .doc-header {
    width: 100%;
    display: table;
    border-bottom: 2px solid #000;
    padding-bottom: 6px;
    margin-bottom: 10px;
  }
  .doc-header-cell { display: table-cell; vertical-align: middle; }
  .doc-header-left { font-size: 13px; font-weight: bold; }
  .doc-header-center { text-align: center; font-size: 13px; font-weight: bold; }
  .doc-header-right { text-align: right; font-size: 11px; font-weight: bold; }

  /* 3-column sticker grid */
  .sticker-grid {
    width: 100%;
    border-collapse: collapse;
    table-layout: fixed;
  }
  .sticker-cell {
    width: 33.33%;
    border: 1px solid #ccc;
    padding: 8px 10px 6px 10px;
    height: 60px;
    vertical-align: top;
  }
  .roll-no {
    font-size: 22px;
    font-weight: bold;
    font-style: italic;
    letter-spacing: 0.5px;
    display: block;
    margin-bottom: 2px;
  }
  .post-name {
    font-size: 10px;
    font-style: italic;
    color: #333;
    display: block;
  }
  .sticker-cell.empty { border: 1px solid #eee; }

  .page-break { page-break-after: always; }
</style>
</head>
<body>
@php
  $perPage = 30; // 10 rows × 3 cols
  $totalRolls = count($roster);
  $pages = ceil($totalRolls / $perPage);
@endphp

@for ($page = 0; $page < $pages; $page++)
@php
  $slice = $roster->slice($page * $perPage, $perPage)->values();
  $pageNum = $page + 1;
@endphp

{{-- Page Header --}}
<div class="doc-header">
  <div style="display:table; width:100%;">
    <div class="doc-header-cell doc-header-left" style="width:35%;">
      {{ strtoupper($centerCity) }}&nbsp;/&nbsp;{{ strtoupper($centerName) }}
    </div>
    <div class="doc-header-cell doc-header-center" style="width:30%;">
      Batch-{{ $batchNumber }}
    </div>
    <div class="doc-header-cell doc-header-right" style="width:35%;">
      Page {{ $pageNum }} of {{ $pages }}&nbsp;&nbsp;&nbsp;
      <span style="font-size:13px; font-weight:bold;">{{ strtoupper($projectName) }}</span>
    </div>
  </div>
</div>

{{-- Sticker Grid --}}
<table class="sticker-grid">
  @for ($row = 0; $row < 10; $row++)
  <tr>
    @for ($col = 0; $col < 3; $col++)
    @php $idx = $col * 10 + $row; $roll = $slice[$idx] ?? null; @endphp
    @if($roll)
    <td class="sticker-cell">
      <span class="roll-no">{{ $roll->roll_no }}</span>
      <span class="post-name">{{ $roll->job->title ?? '' }}</span>
    </td>
    @else
    <td class="sticker-cell empty"></td>
    @endif
    @endfor
  </tr>
  @endfor
</table>

@if($page < $pages - 1) <div class="page-break"></div> @endif
@endfor

</body>
</html>
