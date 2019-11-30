<?php

namespace App\MotoMenus;


class ToCSV
{
    protected $data, $filename;

    /**
     * ToCSV constructor.
     *
     * @param $data
     * @param $filename
     */
    public function __construct($data, $filename)
    {
        $this->data = $data;
        $this->filename = $filename;
    }

    public function getCSV(): string
    {
        // $this->download_send_headers();
        return $this->array2csv();
    }

    private function download_send_headers(): void
    {
        // disable caching
        $now = gmdate("D, d M Y H:i:s");
        header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
        header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
        header("Last-Modified: {$now} GMT");

        // force download
        header("Content-Type: application/force-download");
        header("Content-Type: application/octet-stream");
        header("Content-Type: application/download");

        // disposition / encoding on response body
        header('Content-Disposition: attachment;filename="' . $this->filename .'"');
        header("Content-Transfer-Encoding: binary");
    }

    /**
     * @return null|string
     */
    private function array2csv(): ?string
    {
        if (count($this->data) == 0) {
            return null;
        }

        ob_start();
        $df = fopen("php://output", 'w');
        foreach ($this->data as $row) {
            fputcsv($df, $row);
        }
        fclose($df);

        return ob_get_clean();
    }
}
