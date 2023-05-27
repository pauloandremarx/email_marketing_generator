<?php

namespace app\controllers; 


class Api_produto_manole extends Base
{

 
    public function index($request, $response, $args)
    {

        /*Lista de Json*/  
          
        $url_enviada = filter_input(INPUT_POST, 'url', FILTER_DEFAULT);


        $url_1 = array(
            $url_enviada 
        );

        $context_1 = stream_context_create();
        $content_1 ="";
       
     

        for ($i=0;$i<count($url_1);$i++) {
            $file_get = file_get_contents($url_1[$i], false, $context_1);
            $content_1 = $content_1 . $file_get ;
        }

    

        $re = '/data-product-id="([[\p{L}\p{N}_]+\s?.*)" d.*?category="([[\p{L}\p{N}_]+\s?.*)">\s\s.*?\s\s.*?name="(.*.?)".*price="([[\p{L}\p{N}_]+\s?.*)".*?url="([https:]+\s?.*)">\s\s.*?img.src="([[\p{L}\p{N}_]+\s?.*)".*?$/UmD';


        $re_all = '/<li layout.*?\s\s.*?product-id="([[\p{L}\p{N}_]+\s?.*)" d.*?category="([[\p{L}\p{N}_]+\s?.*)">\s\s.*?\s\s.*?name="(.*.?)".*price="([[\p{L}\p{N}_]+\s?.*)".*?url="([https:]+\s?.*)">\s\s.*?img.src="([[\p{L}\p{N}_]+\s?.*)".*?\s\s.*?\s\s.*?\s\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*>\s\s*?([[\p{L}\p{N}_].*?)\s.*?<\/div>\s.*?\s.*?\s.*?\s.*?\s.*?\s.*>\s\s.*?\s\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s\s*?([R$]*?..*)<\/div>\s.*?\s.*?\s.*?\s.*?\s.*?\s.*\s.*?\s.*pan>*?(....).*\s.*\s.*\s.*\s.*(.........)<\/.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?\s.*?.*?\s.*?\s.*?\s/mU';
        
        /*Primeiro Json*/
        preg_match_all($re_all, $content_1, $matches_1, PREG_SET_ORDER, 0);
        $data_1 = array();
        for ($i=0; $i<count($matches_1); $i++) {
            $data_1 += array($i=>[
                    'skul' => $matches_1[$i][1],
                    'categoria' => $matches_1[$i][2],
                    'nome' => $matches_1[$i][3],
                    'por' => $matches_1[$i][4],
                    'link' => $matches_1[$i][5],
                    'img' => $matches_1[$i][6]
            ]);
        }

        /*Pega a URL original, como ele está na raiz é só passar o nome do arquivo*/
        $url_master_manole_original = file_get_contents('master_manole.json'); 

        /*Salva o url_master_manole_original como um backup*/ 
        file_put_contents('master_manole_bakup.json', $url_master_manole_original); 


        /*Pega a URL original, como ele está na raiz é só passar o nome do arquivo*/
        $json = file_get_contents('master_manole.json'); 
        /*Decodifica para ele se transforma em um ARRAY*/
        $json_decode = json_decode($json, TRUE);


        /*Junta o $data_1 que é um array que veio de um url buscada usando o regex com o json antigo original, quando isso ocorre eles se transformam em um json fundido*/
        $mergeAll_array = array_merge_recursive($data_1, $json_decode);
           /*Agora ele faz uma varredura no array que nasceu da fusão, com essa varredura ele exlcui as duplicadas*/
        $uniqueArray = array_values(array_unique($mergeAll_array, SORT_REGULAR));
         
        /*Recebo em uma variavel o novo array sem duplicadas, ai depois disso converto ele em json*/
        $data_1_json_novo = utf8_encode(json_encode($uniqueArray, JSON_PRETTY_PRINT));

        /*Salvo esse novo json com um novo nome*/
        file_put_contents('novo_master_data_1.json', $data_1_json_novo); 

         /*Como tudo deu certo eu salvo tbm o novo json com o nome do json antigo para não perder os produtos anteriores, e como criei um backup lá em cima, fica tudo ais seguro*/
        file_put_contents('master_manole.json', $data_1_json_novo); 

            //para finalizer eu limpo o cache dos json, para que eles abram com os novos produtos adicionados
            clearstatcache();

        $result = $data_1_json_novo;

        $response->getBody()->write($result);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }
    
    public function all_produtos($request, $response, $args)
    {
        $result = file_get_contents('novo_master_data_1.json'); ;

        $response->getBody()->write($result);
        return $response->withHeader('Content-type', 'application/json')->withStatus(200);
    }
}
