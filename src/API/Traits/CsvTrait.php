<?php

namespace VPMV\Marketo\API\Traits;

trait CsvTrait
{
    public string $csvSeparator = ',';

    /**
     * @param array $headerRow
     * @param array $content
     *
     * @return string
     */
    public function encodeCsv(array $headerRow, array $content): string
    {
        $f = tmpfile();
        fputcsv($f, $headerRow);
        foreach ($content as $row) {
            fputcsv($f, $row);
        }
        $contents = file_get_contents(stream_get_meta_data($f)['uri']);
        fclose($f);

        return $contents;
    }
}
