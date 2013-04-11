<?php

class Upload{

	private $path;
	
	private $resize = true;
	
	private $rename = true;
	
	private $file;
	
	private $new_name;
		
	public function setPath( $path ){
		$this->path = $path;
	}
	
	public function setResize( $resize ){
			$this->resize = $resize;
	}
	
	public function setFile( $file ){
		$this->file = $file;
	}
	
	
	public function upload_file(){
		
		//if( $this->checkDir() === false ) return false;
		//if( $this->isUploaded() === false ) return false;
		if( $this->rename == true ) $this->new_name = microtime(true);
		if( $this->resize === true ) return $this->resize_and_upload();
			
			
		
	}
	
	public function creatDir(){
		if( $this->checkDir() === false )
			mkdir($this->path);
	}
	
	private function uploaded(){
		
	}
	
	private function isUploaded(){
		if( !is_uploaded_file($this->file) ) return false;
		else return true;
	}
	
	private function checkDir(){
		if( !is_dir( $this->path ) ) return false;
		else return true;
	}
	
	private function resize_and_upload(){
		$size = getimagesize($_FILES[''. $this->file .'']['tmp_name']);	// On recupere la taille 
		
		if( $size[0] > 1024 ){
			$newhauteur = $size[1] * (1024 / $size[0]);	// Calcul de la nouvelle hauteur en gardant les proportions
			$img_tmp = ROOT_PATH . 'web' . DS . 'upload'. DS . $_FILES[''. $this->file .'']['name'];
			move_uploaded_file($_FILES[''. $this->file .'']['tmp_name'], $img_tmp);
			$src = imagecreatefromjpeg($img_tmp);
			$img = imagecreatetruecolor(1024, $newhauteur);
			imagecopyresampled($img, $src, 0, 0, 0, 0, 1024, $newhauteur, $size[0], $size[1]);
			$img_name = $this->new_name .'.jpg';
			imagejpeg($img, $this->path . $img_name);
			@unlink($img_tmp);
			return $img_name;
		}else{
			$img = $this->new_name . '.' . substr(strrchr($_FILES[''. $this->file .'']['name'],'.'),1);
			@move_uploaded_file($_FILES[''. $this->file .'']['tmp_name'], $this->path . $img);
			return $img;
		}
	}
	
}