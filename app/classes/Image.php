<?php


namespace app\classes;

use Intervention\Image\ImageManager;
 
function path() {
	$vendorDir = dirname(dirname(__FILE__));
	return dirname($vendorDir);
}

class Image {

	private $intervention;

	private $image;

	private $rename;

	private $resized = false;

	private $resizeWidth;

	private $resizeHeight;

	public function __construct($imageName) {
		$this->intervention = new ImageManager;

		$this->image = $_FILES[$imageName];
	}



	private function rename($name) { 
		$extension = pathinfo($this->image['name'], PATHINFO_EXTENSION); 
		$this->rename = $name."_parte_";
	}

	public function getName() {
		$extension = pathinfo($this->image['name'], PATHINFO_EXTENSION);  
		$name_data = array();
		$target = getimagesize($this->image['tmp_name']);
		$final_da_imagem = $target[1];
		$i=0;
		$contador = 0;
	 
        while ($i <= $final_da_imagem) { 
			$name_data += array('image_'.$contador=>[$this->rename.$contador.".{$extension}"]);
			$i = $i +150; 
			$contador++;
		} 
		$name = json_encode($name_data);

		return $name;
	}

	
	public function size($type) {

		$size = $this->type($type);
 
		$target = getimagesize($this->image['tmp_name']);

		/*deixar quadrado*/ 
		$percent = ($target[0] > $target[1]) ? ($size / $target[0]) : ($size / $target[1]);

		$this->resizeWidth = round(600);

		$this->resizeHeight = round($target[1]);

		$this->type = $type;

		$this->resized = true;

		return $this;

	}

	private function type($type) {
		switch ($type) {
		case 'post':
			$size = 600;
			break;

		default:
			$size = 600;
			break;
		}

		return $size;
	}

	private function doUpload($final_da_imagem, $i, $altura_corte, $contador) {
		 
		$extension = pathinfo($this->image['name'], PATHINFO_EXTENSION); 
		if (!$this->resized) {
			throw new \Exception("Esta faltando você chamar o método size para redimensionar essa foto");
		}

		$image = $this->intervention->make($this->image['tmp_name'])->resize($this->resizeWidth, $this->resizeHeight, function ($constraint) {
			$constraint->aspectRatio();
			$constraint->upsize();
		});

		if ($this->type == 'user') {
			$background = $this->intervention->canvas(190, 190);
			$background->insert($image, 'center');
			$background->save("assets/uploads/{$this->rename}");
		} else {
			
		
			$background = $this->intervention->canvas(600, $final_da_imagem, '#ffffff');
			$background->insert($image, 'center'); 
		
			$background->crop(600, $altura_corte, 0, $i);
			
			$caminho = "assets/uploads/".$this->rename.$contador.".{$extension}";

			$background->save($caminho);  
			
			
		}

	}

	

	
	public function upload() {
		$target = getimagesize($this->image['tmp_name']); 
		$i =0;
		$matematica = 0;
		$count = 0;
		$final_da_imagem = $target[1];
		$altura_corte = 150;
		$name = md5(uniqid()) . time();

		while( $i <= $final_da_imagem){
			$this->rename($name); 
			$this->doUpload($final_da_imagem, $i, $altura_corte, $count);
			$count = 1 + $count;

			/* vai contando até que parte ele vai chegar da imagem, ele começa da altura final da imagem e vai dimunindo*/
			$matematica =  $final_da_imagem - $i;

		

			/*Quando ele estiver faltando apenas 405 para baixar da imagem ele remove os 150 padrão do corte subtraindo pelo valor que está faltando para acabar a imagem*/
			/*Sem esse if, o canvas vai cortar uma parte a mais que não existe no tamanho final da iamgem*/
			if( $matematica <= 300 && $matematica > 150){
				$altura_corte = ($matematica - 150); 


			/*Qundo ele chegar no ultimo corte ele provavlemnte vai ser menor do 150, então o I que sobe de 150 em 150 não pode mais subir de 150. Por isso ele vai receber o valor que está sobrando pra terminar o corte, esse valor está dentro da variavel*/
			}else if($matematica <= 150){
				$altura_corte = ($matematica - 150); 
				$i = $i  + $matematica;
			}

		 

			$i = $i +150;
		
		}; 
	}


	public function delete($photo) {
		unlink(path() . '/public/' . $photo);
	}

}


