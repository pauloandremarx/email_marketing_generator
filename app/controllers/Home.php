<?php

namespace app\controllers;

use app\classes\Flash;
use app\database\models\ModelUser;
use app\database\models\ModelLivros;
use Psr\Container\ContainerInterface;
use app\classes\Image;
use Nyholm\Psr7\Stream;
use ZipStream\Option\Archive;
use ZipStream\ZipStream;
 

class Home extends Base
{
    private $user; 

    public function __construct(ContainerInterface $container)
    {
        $this->user = new ModelUser;
        $this->livro = new ModelLivros;
        $this->container = $container;
    }

    public function index($request, $response)
    { 
       

        return $this->getTwig()->render($response, $this->setView('site/home'), [
            'title' => 'Home',
    
           
        ]);
    }

    public function download($request, $response)
    { 

        $start_html = '<html><head><title>Manole</title> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"></head> <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" align="center" style="border-collapse:collapse;">';
        $end_html = '</body></html>';

        $agora = date('d/m/Y H:i');

        $miolo_html = filter_input(INPUT_POST, 'miolo', FILTER_DEFAULT);

        $miolo_html_decode = json_decode($miolo_html);

        $regex ='/[^A-Z0-9]+/ig'; //remove espaço em branco e caracteres especiais
        
        $filtro_1_date = str_replace($regex, '_', $agora);

        $filtro_2_date = str_replace('', '_', $filtro_1_date);  

        $filtro_3_date = str_replace(' ', '_', $filtro_2_date); 

        $filtro_4_date = str_replace(':', '_', $filtro_3_date); 

        $filtro_5_date = str_replace('/', '_', $filtro_4_date);

        $full_html = $start_html.$miolo_html_decode.$end_html;


        $file_name = 'assets/uploads/email_marketing_'.$filtro_5_date.'_2022.html';
         
          /*Salva o url_master_manole_original como um backup*/ 
          file_put_contents($file_name, $full_html); 

   
  


    // Create ZIP file, only in-memory
    $archive = new Archive();
    $archive->setOutputStream(fopen('php://temp', 'r+'));
    $zip = new ZipStream(null, $archive);
  
    $ImgDirArray = array();
    $HtmlDirArray = array();
     

/*
    if ($handle = opendir('assets/uploads/')) { 
        while (false !== ($entry = readdir($handle))) {
            // ignore hidden files          
            if ($entry != "." && $entry != "..") {
            // only zip specific files
                if ( substr($entry,-3,3) == "jpg" || substr($entry,-3,3) == "png" ) {
                    // if allowed, add them to the array
                    $ImgDirArray[] = $entry;
                }elseif(substr($entry,-4,4) == "html"){
                    $HtmlDirArray[] = json_encode($entry);
                }
            }
        }
        closedir($handle);
    }*/


    if ($handle = opendir('assets/uploads/')) { 
        while (false !== ($entry = readdir($handle))) {
            $i =0;
            
                if ( substr($entry,-3,3) == "jpg" || substr($entry,-3,3) == "png" ) { 
                    $ImgDirArray[] = $entry;
                }elseif(substr($entry,-4,4) == "html"){
                    $HtmlDirArray[] = json_encode($entry);

                }
                $i++;
            }
        }
        closedir($handle);

   
    /*Manda todas as imagens encontradas para dentro do zip, dentro da pasta imagens*/
    $indexCount = count($ImgDirArray);
    sort($ImgDirArray);
    
   
    for($index=0; $index < $indexCount; $index++) {
      
        
            $img_file = str_replace('"', '', $ImgDirArray[$index]);
            $zip->addFileFromPath('images/'.$img_file, 'assets/uploads/'. $img_file );
    }

    /*Manda todos os HTML encontrados para dentro do zip, dentro da pasta rais*/
    $indexCount = count($HtmlDirArray);
  
    for($index=0; $index < $indexCount; $index++) {
        $html_file = str_replace('"', '', $HtmlDirArray[$index]);
        $zip->addFileFromPath($html_file, 'assets/uploads/'.$html_file);
    }
   
    $zip->finish();


    /*Apagar todos os arquivos da pasta X*/

    

    if ($handle = opendir('assets/uploads/')) { 
        while (false !== ($entry = readdir($handle))) {
            // ignore hidden files          
            if ($entry != "." && $entry != "..") {
            // only zip specific files
                if ( substr($entry,-3,3) == "jpg" || substr($entry,-3,3) == "png" || substr($entry,-4,4) == "html" ) {
                    // if allowed, add them to the array
                     unlink('assets/uploads/'.$entry);
                } 
            }
        }
        closedir($handle);
    }
 

    $response = $response
    ->withHeader('Content-Type', 'application/zip')
    ->withHeader('Content-Disposition', 'attachment; filename="email_marketing.zip"');




  

    // nyholm\Psr7
    return $response->withBody(Stream::create($archive->getOutputStream()));
     

    /*
    $filename = 'email_marketing-'.$filtro_4_date.'.zip';
        
        $page = $response->withHeader('Content-Description', 'File Transfer')
       ->withHeader('application/x-zip-compressed', 'application/octet-stream')
       ->withHeader('Content-Disposition', 'attachment;filename="'.$filename .'"')
       ->withHeader('Expires', '0');
     

       

        return $page;*/

        $response = $response
    ->withHeader('Content-Type', 'application/zip')
    ->withHeader('Content-Disposition', 'attachment; filename="filename.zip"');

    // nyholm\Psr7
    return $response->withBody(Stream::create($archive->getOutputStream()));
 
         
    }

    public function red($request, $response)
    { 
        $filename = 'JSON_MANOLE.json';
        date_default_timezone_set("America/Sao_Paulo");
        
        if (file_exists($filename)) {
            $json_date =  date ("F d Y H:i:s.", filemtime($filename));
        }else{$json_date =0;}

        $message = Flash::get('message'); 
 
        return $this->getTwig()->render($response, $this->setView('site/red'), [
            'title' => 'RED', 
            /*'produtos' => $matches_1,*/
            'tempo_carregamento' =>  "3",
            'message' => $message,
            'json_date' => $json_date,
        ]);
    }

 
    public function gerador_email($request, $response)
    { 
         
        $message = Flash::get('message'); 

        $start_html = '<!DOCTYPE-_007 html><html-_007><head-_007> <title>Manole</title> <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> </head> <body bgcolor="#FFFFFF" leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" align="center" style="border-collapse:collapse;"><head-_007><body_-_007>';

        $end_html = '</body-_007></html-_07>';
 
        return $this->getTwig()->render($response, $this->setView('site/gerador_email'), [
            'title' => 'Gerador de email', 
            'tempo_carregamento' =>  "3",
            'message' => $message,
            'start_html' => $start_html,
            'end_html' => $end_html  
        ]);
    }


    public function preco($request, $response)
    {
        function timePHPProcess($start = null) {
            $mTime = microtime(); // Pega o microtime
            $mTime = explode(' ',$mTime); // Quebra o microtime
            $mTime = $mTime[1] + $mTime[0]; // Soma as partes montando um valor inteiro
             
            if ($start == null)
            return $mTime;
            else
            return round($mTime - $start, 2);
            }
            

        $startTIME = timePHPProcess(); 

        $message = Flash::get('message'); 
 
        $endTIME = timePHPProcess($startTIME);
 

        $filename = 'JSON_MANOLE.json';
        date_default_timezone_set("America/Sao_Paulo");
        
        if (file_exists($filename)) {
            $json_date =  date ("F d Y H:i:s.", filemtime($filename));
        }else{$json_date =0;}

        return $this->getTwig()->render($response, $this->setView('site/preco'), [
            'title' => 'preco', 
            /*'produtos' => $matches_1,*/
            'tempo_carregamento' =>  $endTIME,
            'message' => $message,
            'json_date' => $json_date,
        ]);
    }


    public function gravarImagem($request, $response, $args)
    { 
        if ($_FILES["banner"]['error'] == UPLOAD_ERR_OK) {
            
            $image = new Image('banner');
            $image->size('post')->upload();
 
            $data = "";
     
            $data = array([
                    'banner' => $image->getName(),
            ]);
      

            $result = utf8_encode(json_encode($data));
        
            $response->getBody()->write($result);

            return $response->withHeader('Content-type', 'application/json')->withStatus(200);
        }

   
     
    }
}


