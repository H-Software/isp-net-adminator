<?php

namespace App\Print;

use Exception;

class printRegForm
{
    public $file_name;  //file name of generated pdf file
    public $id_cloveka; //internal key from DB, where if generate file for existing object

    //form vars
    public $input_ec;
    public $input_jmeno_a_prijmeni;
    public $input_adresa_odber;
    public $input_adresa_tr_byd;
    public $input_pozadovany_tarif;

    public $form_ec;

    //
    // load_input_vars
    //
    public function load_input_vars()
    {
        reset($_POST);

        foreach ($_POST as $name => $value) {
            if(preg_match("/^input\_/", $name) == 1) {
                $this->$name = htmlspecialchars($value);
                //zde pripadne doplnovani pomlcek
            }
        }
    }

    //
    // generate_pdf_file
    //
    public function generate_pdf_file()
    {

       // define('FPDF_FONTPATH', "include/font/");

        //zaklad, vytvoreni objektu a pridani stranky
        try {
            $pdf = new \FPDF("P", "mm", "A4");
        } catch (Exception $e) {
            die("cant create class for PDF: ".var_export($e->getMessage(), true));
        }

        // $pdf->Open();
        $pdf->AddPage();

        // ceskej arial
        $pdf->AddFont('arial', '', 'arial.php');

        // autor a podobny hemzy

        //Nastaví autora dokumentu.
        $pdf->SetAuthor("ISP Net Adminator3");

        //Nastaví tvůrce dokumentu (většinou název aplikace)
        $pdf->SetCreator("Registrační formulář");

        //Titulek dokumentu
        $pdf->SetTitle("Reg. Formulář");

        // vlozeni obrazku na pozadi
        // TODO: fix missing image
        // $img="img2/print/2012-05-form.jpg";
        // $pdf->Image($img,0,0,210);

        $pdf->SetFont('Arial', '', 10);

        $pdf->Cell(0, 1, '', 0, 1);

        //zacatek formu - Ev. Cislo
        $pdf->Cell(145);
        $pdf->Cell(50, 0, $this->input_ec, 0, 1);

        //
        //sekce zakaznik
        //
        $pdf->Cell(0, 70, "", 0, 1);

        //1.radka
        $pdf->Cell(37, 5);
        $pdf->Cell(20, 5, iconv("UTF-8", "CP1250", $this->input_jmeno_a_prijmeni), 0, 0);

        $pdf->Cell(77, 5);
        $pdf->Cell(20, 5, iconv("UTF-8", "CP1250", $this->input_adresa_odber), 0, 1);

        //2.radka
        $pdf->Cell(37, 5);
        $pdf->Cell(20, 5, iconv("UTF-8", "CP1250", $this->input_adresa_tr_byd), 0, 0);

        $pdf->Cell(77, 5);
        $pdf->Cell(20, 5, iconv("UTF-8", "CP1250", $this->input_pozadovany_tarif), 0, 1);

        //POZNAMKA 2
        $pdf->Cell(0, 10, '', 0, 1);

        $pdf->Cell(8);
        $pdf->MultiCell(175, 7, $poznamka2, 0, 1);
        // $pdf->Cell(21); $pdf->Cell(5,5,$celk_cena_s_dph,0,1);

        //end of inputs arrays

        $datum_nz = date('Y-m-d-H-i-s');

        if($this->id_cloveka > 0) {
            $this->file_name = "/print/temp/reg-form-v3-id-".$this->id_cloveka."-".$datum_nz.".pdf";
        } else {
            $this->file_name = "/print/temp/reg-form-v3-ec-".$this->form_ec."-".$datum_nz.".pdf";
        }

        $pdf->Output(__DIR__ . "/../../.." . $this->file_name, "F");

    } //end of function "generate_pdf_file"

} //end of class "print_reg_form"
