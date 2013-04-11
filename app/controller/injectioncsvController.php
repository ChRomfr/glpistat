<?php

class injectioncsvController extends Controller{
	
	public function clubAction(){
		set_time_limit(0);
		
		$File = 'club_ok_sans_doublons';
		$Table = 'clubs';
		
		$Fichier = ROOT_PATH  . 'web' . DS . 'upload' . DS . 'csv' . DS . $File .'.csv';
		$Contenu = file($Fichier);
		$i=0;
		foreach($Contenu as $Row):
			$Data = explode(';',$Row);
			if( !empty($Data[7]) && $this->app->db->count('import.'. $Table, array('email =' => $Data[7])) == 0 ):
				
				// Construction de l array
				$DataSave = array(
					'nom'		=>	$Data[0],
					'email'		=>	$Data[7],
					'categorie'	=>	$Data[9],
				);
				
				// Injection dans la base
				$this->app->db->insert('import.'. $Table,$DataSave);
				$i++;
			endif;
		endforeach;
		
		echo "Operation terminee. <br/> ". $i ."email ont ete injecte dans la base";
	}
	
}