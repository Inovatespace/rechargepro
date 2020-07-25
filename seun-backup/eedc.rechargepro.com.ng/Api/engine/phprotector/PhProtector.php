<?php
  //****************************************************************
  // Web page     : http://code.google.com/p/phprotector
  // Autor        : Hugo Sousa 		adamastor666@gmail.com
  // Date         : 2010-03-25
  // Nome         : inc_sql_logger.php
  // Description  : Log functions (XML) 
  // Version      : 0.3.1.1            
  //                  
  //***************************************************************
  require("LogAtack.php");
  
  class PhProtector
  {	
	  var $XML_LOG; //log file path
	  var $SHOW_ERRORS;  
	  var $lg;  //log class pointer
		  
	  
	  public function __construct($log_path, $show_errors){
		 
		  $this->XML_LOG = $log_path;
		  
		  $this->SHOW_ERRORS=$show_errors;
		   
		  $this->lg = new LogAtack($this->XML_LOG); 
		  
		 
		  if ($this->SHOW_ERRORS){
			  error_reporting(E_ERROR | E_WARNING | E_PARSE);   //Show errors
			  //ini_set(display_errors, "1");  //display errors
		  }else{
			   ini_set(display_errors, "0"); //display errors
			  //ini_set(log_errors, "1");  //log_errors
		  }
		  
	  }
	  
	  
	  
	  /*
	  * Main function to be called in a index page that redirects to other pages
	  *
	  */
  
	  public function isMalicious(){		
		 $sqli = false;
		 
		  $num_bad_words1 = $this->CheckGet();
		  $num_bad_words2 = $this->CheckPost();
		  
		  if ($num_bad_words1 > 0){ 
			  $this->lg->LogData($num_bad_words1, 1);
			  $sqli = true;
		  }
			  
		  if ($num_bad_words2 > 0){  //if sql injection we log data
				$this->lg->LogData($num_bad_words2, 2);
			  $sqli = true;
		  }
		  
		  return $sqli;
	  }
  
  
	  /*
	  * -------------------------------------------------------------------------
	  *
	  */
	  
	  //check for sql injection and XSS in Post variables
	  private function CheckPost(){	
		  $num_bad_words = 0;
		  
		  foreach($_POST as $campo => $input){
			  $_POST[$campo]= htmlentities((string) $_POST[$campo],ENT_QUOTES,'ISO-8859-1');  // XSS PROTECTION
			  
			  $num_bad_words = $num_bad_words + $this->wordExists($input);   //SQL INJECTION
		  }
		  
		  return $num_bad_words;
	  }
	  
	  
	  //check for sql injection and XSS in GET variables
	  private function CheckGet(){
		  $num_bad_words = 0;
		  
			  foreach($_GET as $campo => $input){
				   $_GET[$campo]= htmlentities((string) $_GET[$campo],ENT_QUOTES,'ISO-8859-1');  // XSS PROTECTION
				   
				   if($this->isIdInjection($campo,$input)){   //SQL ID INJECTION    
					  $num_bad_words =  $num_bad_words + 0.5;      
				   }
					
				   $num_bad_words = $num_bad_words + $this->wordExists($input);   //SQL INJECTION
			  }
					
		  return $num_bad_words;		 				  	
	  }
	  
	  /**
	  *	return true if injection sql word is found.
	  *	The input is tested if is equal to a sql injection pattern 
	  *	\b[^a-z]*?drop[^a-z]*?\b
	  *   http://www.pagecolumn.com/tool/regtest.htm
	  **//*     "/*","+"        */
		  
	  private function wordExists($input){	
		  
		  $num_bad_words = 0;
		  
		  /*		
		   WORD AFTER
		  */	
		  $baddelim1 = "[^a-z]*";  //the delim should be from "a" to "b" anything else is considered sql injection :)
		  $baddelim2 = "[^a-z]+";
		  $badwords= array("union", "select", "show", "insert", "update", "delete", "drop", "truncate", "create", "load_file", "exec", "#", "--");
		  //"/*"
		  foreach($badwords as $badword){ 
			  $expression = "/".$baddelim1.strtolower($badword).$baddelim2."/";
			  //print $expression."<br>";
			  if (preg_match ($expression, strtolower($input))) {
				  //die("sql injection!");
				  $num_bad_words++;
			  }
		  }	
				  
		  
		  /*
		  BEFORE WORD 
		  */
		  $baddelim1 = "[^a-z]+";  //the delim should be from "a" to "b" anything else is considered sql injection :)
		  $baddelim2 = "[^a-z]*";
		  $badwords= array("@@version", "@@datadir", "user", "version");
		  
		  foreach($badwords as $badword){ 
			  $expression = "/".$baddelim1.strtolower($badword).$baddelim2."/";
			  //print $expression."<br>";
			  if (preg_match ($expression, strtolower($input))) {
				  //die("sql injection!");
				  $num_bad_words++;
			  }
		  }	
		  
		  
		  /*
		  BEFORE WORD AFTER
		  */
		  $baddelim1 = "[^a-z]+";  //the delim should be from "a" to "b" anything else is considered sql injection :)
		  $baddelim2 = "[^a-z]+";
		  $badwords= array("benchmark", "--", "varchar", "convert", "char", "limit", "information_schema","table_name", "from", "where", "order");
		  
		  foreach($badwords as $badword){ 
			  $expression = "/".$baddelim1.strtolower($badword).$baddelim2."/";
			  //print $expression."<br>";
			  if (preg_match ($expression, strtolower($input))) {
				  //die("sql injection!");
				  $num_bad_words++;
			  }
		  }	
		  
			  
		  return $num_bad_words;
	  }
	  
	  
	  /**
	  *	return true if and ID is not really an ID!
	  *
	  **/		
	  private function isIDInjection($campo,$input){
		  $reg="/^id/";
			  
			  if(preg_match($reg, $campo)){
				  if(!$this->stringIsNumberNotZero($input) || $input == ''){
					  return true;   // if is ID and NOT INTEGER or NULL -> SQL INJECTION!!
				  }   
			  }	
			  
		  return false;
	  }
	  
	  
	  /**
	  *	return true if the string is a number (diferent from 0, the id could not be zero!)
	  *
	  **/	
	  private function stringIsNumberNotZero( $string ){
	  $i=0;
		  while ( $i < strlen($string) ){
			  if ($string{$i} == "0" && $i==0)
				  return false;
			  
			  //verifica se é numero
			  if ( $string{$i} != "0" && 
				  $string{$i} != "1" && 
				  $string{$i} != "2" && 
				  $string{$i} != "3" && 
				  $string{$i} != "4" && 
				  $string{$i} != "5" && 
				  $string{$i} != "6" && 
				  $string{$i} != "7" && 
				  $string{$i} != "8" && 
				  $string{$i} != "9"  ) 
			  return false;
			  $i++;
		  }//while
	  
	  return true;
	  }
	  
	  
	  
	  
	  
  } //end class  
?>