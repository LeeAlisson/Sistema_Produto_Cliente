<?php

namespace App\Support;

class CsvResponse
{
  public static function send(string $filename, array $headers, array $rows): void
  {
    header('Content-Type: text/csv; charset=utf-8');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Pragma: no-cache');

    $out = fopen('php://output', 'w');
    fwrite($out, "\xEF\xBB\xBF"); // Excel reconhece UTF-8
    fputcsv($out, $headers, ';');

    foreach ($rows as $row) {
      fputcsv($out, $row, ';');
    }

    fclose($out);
    exit;
  }
}
