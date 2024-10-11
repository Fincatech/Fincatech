<?php

nameSpace HappySoftware\Controller\Traits;

use \Mpdf\Mpdf;

trait PDFTrait{

    public $mpdf = null;
    public $pdfFileName;
    //  Variable para almacenar el error al escribir en el PDF
    public $pdfError;
    //  Footer
    public $pdfFooter;

    /**
     * Establece el footer del archivo
     */
    public function SetPDFFooter($value){ $this->pdfFooter = $value; }
    /**
     * Devuelve el footer asignado al PDF
     */
    public function PDFFooter(){ return $this->pdfFooter; }

    /**
     * Inicializa el componente de PDF
     */
    public function InitializePDF($fileName)
    {

        //  Opciones del PDF
        $config = [
            'mode'          => 'utf-8',
            'format'        => 'A4',
            'margin_left'   => 10,
            'margin_top'    => 10,
            'margin_right'  => 10,
            'margin_bottom' => 10
        ];

        //  Inicializamos el componente si no lo ha hecho ya
        if(is_null($this->mpdf)){
            $this->mpdf = new \Mpdf\Mpdf($config);
            $this->mpdf->SetAuthor('Fincatech Software SL');   
            // $this->mpdf->useSubstitutions = false;
            // $this->mpdf->shrink_tables_to_fit = 1;
            // $this->mpdf->simpleTables = true;
            // $this->mpdf->packTableData = true;
            // // autosize wins
            // $this->mpdf->tableMinSizePriority = true;                     
        }

        //  Asignamos el nombre del fichero a la ruta de pdf
        $extension = strpos($fileName, '.pdf') === FALSE ? '.pdf' : '';
        $this->pdfFileName = ROOT_DIR . $fileName . $extension;   

        //  Escribimos la cabecera del fichero
        // $this->mdpf->WriteHTML('<html><body>');
    }

    /**
     * Escribe un html en un fichero PDF
     * @param string $html HTML a escribir en el PDF
     * @param string $fileName (optional). Nombre del fichero. Defaults: timestamp
     * @param bool  $makePageBreak (optional). Crear salto de página. Defaults: false
     * @param bool $overwrite (optional). Indica si se ha de escribir sobre el mismo fichero u otro nuevo
     */
    public function WriteToPDF($html, $footer = null, $includePageBreak = false, $finalPage = false )
    {

        try{

            if(!is_null($footer)){
                $this->mpdf->SetHTMLFooter($footer);
            }

            //  Escribimos el contenido
            $this->mpdf->WriteHTML($html);

            //  Salto de página
            if($includePageBreak && !$finalPage){
                $this->mpdf->AddPage();
            }

            return true;
        }catch(\Exception $ex){
            $this->pdfError = $ex->getMessage();
            return false;
        }

    }

    /**
     * Escribe el contenido del pdf en disco y devuelve el nombre junto con la ruta de ubicación
     * @param string $destination. Destino del PDF (Disco, BLOB,...)
     * @return string Nombre del fichero o blob
     */
    public function MakePDF($destination = null)
    {
        error_reporting(0);
        //  Si no está especificado el destino de escritura, se establece a disco directamente
        if(is_null($destination)){
            $destination = \Mpdf\Output\Destination::FILE;
        }

        //  Si ya existía, lo eliminamos del sistema
        if(file_exists($this->pdfFileName)){
            unlink($this->pdfFileName);
        }

        //  Escribimos el fichero en el servidor
        $this->mpdf->Output($this->pdfFileName, $destination);
        //  Liberamos el componente
        $this->mpdf = null;
        //  Devolvemos el nombre del fichero
        return $this->pdfFileName;
    }




}