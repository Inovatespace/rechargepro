<?php
    //****************************************************************
    // Web page     : http://code.google.com/p/phprotector
    // Autor        : Hugo Sousa 		adamastor666@gmail.com
    // Date         : 2010-03-25 
    // Nome         : inc_sql_logger.php
    // Description  : Log functions (XML)  
    // Version      : 0.3.1.1 
    //                  
    //****************************************************************
    
    class LogAtack
    {
      var $XML_LOG;
    
        
      public function __construct($log_path){
        $this->XML_LOG = $log_path;     
      }
    
    
       public  function LogData($score, $request_type){	
            define("DATE_FORMAT","d-m-Y H:i:s");
    
            $date     = date(DATE_FORMAT);
            
            $ip       = ( isset($_SERVER['REMOTE_ADDR']) 
                                  && ($_SERVER['REMOTE_ADDR'] != ""))     
                              ? $_SERVER['REMOTE_ADDR']     : "Unknown";
            
            $hostname = gethostbyaddr($ip);
            
            $browser  = ( isset($_SERVER['HTTP_USER_AGENT']) 
                                 && ($_SERVER['HTTP_USER_AGENT'] != "")) 
                             ? $_SERVER['HTTP_USER_AGENT'] : "Unknown";
                                                     
            $request= ( isset($_SERVER['REQUEST_URI'])
                                  && ($_SERVER['REQUEST_URI'] != ""))
                              ? $_SERVER['REQUEST_URI']     : "Unknown";
            
            if ($request_type == 2){
                $request= $request."     =>";
                foreach($_POST as $campo => $input){
                    $request= $request. "\$_POST[$campo]=".$input.", ";
                }
            }
                              
                             
            $referer  = ( isset($_SERVER['HTTP_REFERER']) 
                                 && ($_SERVER['HTTP_REFERER'] != ""))
                             ? $_SERVER['HTTP_REFERER']    : "Unknown";
                
            $logs = $this->OpenXmlLog(); //if they exist...
            
            $i = count($logs);  //next index
            $logs[$i]['date'] = $date;
            $logs[$i]['ip'] = $ip;
            $logs[$i]['hostname'] = $hostname;
            $logs[$i]['browser'] = $browser;
            $logs[$i]['request'] = $request;
            $logs[$i]['score'] =  $score;
            $logs[$i]['referer'] =  $referer;
            
            $this->SaveXmlLog($logs);
        }
         
    /*
    * Reads all lines of XML log to $logs global array
    *
    */
        private function OpenXmlLog(){
        
            $logs = array();  
            $reader = new XMLReader();
            
            
            if(!file_exists($this->XML_LOG)){
              $file = fopen($this->XML_LOG,"w+");
              $content = '<?xml version="1.0" encoding="ISO-8859-1"?>
<?xml-stylesheet type="text/xsl" href=".log_style.xsl"?>
<!--PhProtector logs-->
<logs>
</logs>';
              fwrite($file,$content);
              fclose($file);
            }
            
            if(!@$reader->open($this->XML_LOG))
            { 
                return $logs;
            }
            
            $i=0;	
            while ($reader->read()) {
               
               if ($reader->nodeType == XMLREADER::ELEMENT) {  
               switch ($reader->name) {
                    case "date":
                        $reader->read();
                        $logs[$i]['date'] = $reader->value;
                    break;
                    case "ip":
                        $reader->read();
                        $logs[$i]['ip'] = $reader->value;
                    break;
                    case "hostname":
                        $reader->read();
                        $logs[$i]['hostname'] = $reader->value;
                    break;
                    case "browser":
                        $reader->read();
                        $logs[$i]['browser'] = $reader->value;
                    break;
                    case "request":
                        $reader->read();
                        $logs[$i]['request'] = $reader->value;
                    break;
                    case "score":
                        $reader->read();
                        $logs[$i]['score'] =  $reader->value;
                    break;
                    case "referer":
                        $reader->read();
                        $logs[$i]['referer'] =  $reader->value; 
                        $i++;
                    break;
                }
                
               }
            }  //end while
            
        
            $reader->close();
            
            return $logs;
        }
    
    
    /*
    * Saves all of lines $logs global array to XML file in disk...
    *
    */
    private function SaveXmlLog($logs){
            
            # Instancia do objeto XMLWriter
            $xml = new XMLWriter;
            
            # Cria memoria para armazenar a saida
            $xml->openMemory();
            
            $xml->setIndent(true);
    
            # Inicia o cabeçalho do documento XML
            $xml->startDocument( '1.0' , 'iso-8859-1' );
            $xml->writePi('xml-stylesheet' , 'type="text/xsl" href=".log_style.xsl"');
            
            $xml->writeComment('PhProtector logs');
    
            # Adiciona/Inicia um Elemento / Nó Pai <item>
            $xml->startElement("logs");
            
             for($i=0; $i< count($logs); $i++){	
                //print $i;
                $xml->startElement('log'); 
                #  Adiciona um Nó Filho <quantidade> e valor 8
                $xml->writeElement("date", $logs[$i]['date']);
                $xml->writeElement("ip", $logs[$i]['ip'] );
                $xml->writeElement("hostname", $logs[$i]['hostname']);
                $xml->writeElement("browser", $logs[$i]['browser']);
                $xml->writeElement("request", $logs[$i]['request']);
                $xml->writeElement("score", $logs[$i]['score']);
                $xml->writeElement("referer", $logs[$i]['referer']);
                $xml->endElement();	 
             }
            #  Finaliza o Nó Pai / Elemento <Item>
            $xml->endElement();
            //lets output the memory to our file variable,and we gonna put that variable inside the file we gonna create
            /*
            header("Content-type: text/xml");
            print $xml->outputMemory(true);
             die();
            */
            $file = fopen($this->XML_LOG,'w+');
            fwrite($file,$xml->outputMemory(true));
            fclose($file);
    }
    
    /*
    function ShowXmlLog($logs){  //debug only!
         for($i=0; $i< count($logs); $i++){
            print $logs[$i]['date'];
            print $logs[$i]['ip'];
            print $logs[$i]['hostname'];
            print $logs[$i]['browser'];
            print $logs[$i]['request'];
            print $logs[$i]['score'];
            print $logs[$i]['referer'];
            print "<br>";
         }	 
    }
    */
    
	
	
	
	
	
	
	}
    
    /*
    function SaveHtmlLog($num_bad_words){
    
            define("DATE_FORMAT","d-m-Y H:i:s");
            define("LOG_FILE","include/sql_atacks.html");
    
            $logfileHeader='
            <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
            <html>
            <style type="text/css">
                
            </style>
            <head>
               <title>Visitors log</title>
            </head>
            <body>
              <table cellpadding="0" cellspacing="1" border="1">
                <tr>
                  <th>DATE</th>
                  <th>IP</th>
                  <th>HOSTNAME</th>
                  <th>BROWSER</th>
                  <th>URI</th>
                  <th>SCORE</th>
                  <th>REFERRER</th></tr>'."\n";
    
                $userAgent = ( isset($_SERVER['HTTP_USER_AGENT']) 
                                 && ($_SERVER['HTTP_USER_AGENT'] != "")) 
                             ? $_SERVER['HTTP_USER_AGENT'] : "Unknown";
                             
                $userIp    = ( isset($_SERVER['REMOTE_ADDR']) 
                                  && ($_SERVER['REMOTE_ADDR'] != ""))     
                              ? $_SERVER['REMOTE_ADDR']     : "Unknown";
                              
                $refferer  = ( isset($_SERVER['HTTP_REFERER']) 
                                 && ($_SERVER['HTTP_REFERER'] != ""))
                             ? $_SERVER['HTTP_REFERER']    : "Unknown";
                             
                $uri       = ( isset($_SERVER['REQUEST_URI'])
                                  && ($_SERVER['REQUEST_URI'] != ""))
                              ? $_SERVER['REQUEST_URI']     : "Unknown";
            
                $hostName   = gethostbyaddr($userIp);
                $actualTime = date(DATE_FORMAT);
            
                if ($num_bad_words <= 0.5 ){
                    $cor="#ffffff";
                }else{
                    $cor="#ff0000";
                }
                
                print $num_bad_words." "."0.5";
            
                $logEntry = " <tr> 
                    <td bgcolor=$cor>   $actualTime</td>
                    <td bgcolor=$cor>  $userIp</td>
                    <td bgcolor=$cor>  $hostName</td>
                    <td bgcolor=$cor>  $userAgent</td>
                    <td bgcolor=$cor>  $uri</td>
                    <td bgcolor=$cor>  $num_bad_words</td>
                    <td bgcolor=$cor>  $refferer</td>
                </tr>\n";
            
                if (!file_exists(LOG_FILE)) {
                    $logFile = fopen(LOG_FILE,"w");
                    fwrite($logFile, $logfileHeader);
                }
                else {
                    $logFile = fopen(LOG_FILE,"a");
                }
            
                fwrite($logFile,$logEntry);
                fclose($logFile);
    }
    */
?>