<?php

class print_reg_form
{
    
    //
    // variables
    //
    var $file_name;  //file name of generated pdf file
    var $id_cloveka; //internal key from DB, where if generate file for existing object
    
    //form vars
    var $input_ec;
    var $input_jmeno_a_prijmeni;
    var $input_adresa_odber;
    var $input_adresa_tr_byd;
    var $input_pozadovany_tarif;
    
    
    //
    //  functions
    //
    
    //
    // load_input_vars
    //
    
    public function load_input_vars(){
    
	 reset ($_POST);
	 
	 while ( list($name, $value) = each($_POST) ){
	 
	    if(preg_match("/^input_/",$name) == 1){
		
		$this->$name = htmlspecialchars($value);
	    
		//zde pripadne doplnovani pomlcek
	    
	    } //end of if(preg_math(...
	    
	 } //end of while	    
	     
    } //end of function "load_input_vars"

    //
    // generate_pdf_file
    //
    
    public function generate_pdf_file(){
	
	
	define('FPDF_FONTPATH',"include/font/");
	// require_once("include/fpdf.class.php");

	//zaklad, vytvoreni objektu a pridani stranky
	try {
		$pdf = new \fPDF("P","mm","A4");
	}
	catch (Exception $e) {
		die("cant create class for PDF: ".var_export($e->getMessage(),true));
	}

	$pdf->Open();
	$pdf->AddPage();

	// ceskej arial
	$pdf->AddFont('arial','','arial.php');

	// autor a podobny hemzy

	//Nastaví autora dokumentu.
	$pdf->SetAuthor("ISP Net Adminator3");

	//Nastaví tvůrce dokumentu (většinou název aplikace)
	$pdf->SetCreator("Registrační formulář");

	//Titulek dokumentu
	$pdf->SetTitle("Reg. Formulář");
	
	// vlozeni obrazku na pozadi
	$img="img2/print/2012-05-form.jpg";
	$pdf->Image($img,0,0,210);

	$pdf->SetFont('Arial','',10);

	$pdf->Cell(0,1,'',0,1);

	//zacatek formu - Ev. Cislo
	$pdf->Cell(145); 
	$pdf->Cell(50,0,$this->input_ec,0,1);
	
	//
	//sekce zakaznik
	//
	$pdf->Cell(0,70,"",0,1); 
	
	//1.radka
	$pdf->Cell(37,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_jmeno_a_prijmeni),0,0);
	
	$pdf->Cell(77,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_adresa_odber),0,1);
	
	//2.radka
	$pdf->Cell(37,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_adresa_tr_byd),0,0);
	
	$pdf->Cell(77,5); 
	$pdf->Cell(20,5,iconv("UTF-8","CP1250", $this->input_pozadovany_tarif),0,1);
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	//POZNAMKA 2
	$pdf->Cell(0,10,'',0,1);
	  
	$pdf->Cell(8); 
	$pdf->MultiCell(175,7,$poznamka2,0,1);
	// $pdf->Cell(21); $pdf->Cell(5,5,$celk_cena_s_dph,0,1);
	   
	//end of inputs arrays
	
	$datum_nz = date('Y-m-d-H-i-s');
	    
	if( $this->id_cloveka > 0 )
	{ $this->file_name = "print/temp/reg-form-v3-id-".$this->id_cloveka."-".$datum_nz.".pdf"; }
	else
	{ $this->file_name = "print/temp/reg-form-v3-ec-".$this->form_ec."-".$datum_nz.".pdf"; }
	        
	$rs = $pdf->Output($this->file_name,"F");
	         
    } //end of function "generate_pdf_file"

} //end of class "print_reg_form"
