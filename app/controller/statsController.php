<?php

class statsController extends Controller{
    
    public function indexAction(){
        
       $Date = array('month' => date('m'), 'year' => date('Y') ); // contient mois et annee pour les stats
       
       // Verification si date passe en parametre
       if( $this->app->HTTPRequest->getExists('month') && $this->app->HTTPRequest->getExists('year') ):
            $Date['month'] = $this->app->HTTPRequest->getData('month');
            $Date['year'] = $this->app->HTTPRequest->getData('year');
			
			if( empty($Date['month']) ):
				$Tmp =	$this->app->db->select( 'count(id) as nb_ticket')
						->from('glpi_tickets ggt')
						->where_free('ggt.date LIKE "'. $Date['year'] .'-%"')
						->get_one();
				$this->app->smarty->assign('NbTickets',$Tmp['nb_ticket']);			

                // NbTicket clos
                $Tmp =   $this->app->db->select( 'count(id) as nb_ticket')
                            ->from('glpi_tickets ggt')
                            ->where_free('ggt.closedate LIKE "'. $Date['year'] .'-%" AND status = "closed"')
                            ->get_one();
                $this->app->smarty->assign('NbTicketsClosed',$Tmp['nb_ticket']);

                // NbTicket resolu
                $Tmp =   $this->app->db->select( 'count(id) as nb_ticket')
                            ->from('glpi_tickets ggt')
                            ->where_free('ggt.solvedate LIKE "'. $Date['year'] .'-%" AND status = "solved"')
                            ->get_one();
                $this->app->smarty->assign('NbTicketsSolved',$Tmp['nb_ticket']);
            else:
                $Tmp =  $this->app->db->select( 'count(id) as nb_ticket')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date LIKE "'. $Date['year'] .'-'. $Date['month'] .'-%"')
                        ->get_one();
                $this->app->smarty->assign('NbTickets',$Tmp['nb_ticket']);          

                // NbTicket clos
                $Tmp =   $this->app->db->select( 'count(id) as nb_ticket')
                            ->from('glpi_tickets ggt')
                            ->where_free('ggt.closedate LIKE "'. $Date['year'] .'-'. $Date['month'] .'-%" AND status = "closed"')
                            ->get_one();
                $this->app->smarty->assign('NbTicketsClosed',$Tmp['nb_ticket']);

                // NbTicket resolu
                $Tmp =   $this->app->db->select( 'count(id) as nb_ticket')
                            ->from('glpi_tickets ggt')
                            ->where_free('ggt.solvedate LIKE "'. $Date['year'] .'-'. $Date['month'] .'-%" AND status = "solved"')
                            ->get_one();
                $this->app->smarty->assign('NbTicketsSolved',$Tmp['nb_ticket']);
            endif;

       endif;
       
       $this->app->smarty->assign(array(
        'Date'  =>  $Date,
       ));
       
       return $this->app->smarty->fetch( VIEW_PATH . 'stats' . DS . 'index.tpl');
       
    }
    
    public function statsAction(){
        $Filtre = $this->app->HTTPRequest->postData('filtre');
        
        $dd = $Filtre['date_debut'];
        $df = $Filtre['date_fin'];
		
		// Si aucun date on retourne la page d index
		if( empty($dd) || empty($df) ):
			return $this->indexAction();
		endif;
        
        $tmp = explode('/',$dd);
        $Filtre['date_debut'] = $tmp[2] .'-'. $tmp[1] .'-'. $tmp[0];
        
        $tmp = explode('/',$df);
        $Filtre['date_fin'] = $tmp[2] .'-'. $tmp[1] .'-'. $tmp[0];
        
        $this->app->db->select('COUNT(id) as nb')->from('glpi_tickets ggt');
        
        if( !empty($Filtre['lieu']) ):
            $this->app->db->where(array('locations_id =' => $Filtre['lieu']));
        endif;
        
        if( !empty($Filtre['categorie']) ):
            $this->app->db->where(array('itilcategories_id =' => $Filtre['categorie']));
        endif;
        
        $this->app->db->where_free(' ggt.date >= "'.$Filtre['date_debut'].' 00:00:00" ');
        $this->app->db->where_free(' ggt.date <= "'.$Filtre['date_fin'].' 23:59:59" ');
        
        $Tmp = $this->app->db->get_one();
        
        $Lieux = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        $Categories = $this->app->db->select('id, completename')->from('glpi_itilcategories gtc')->order('completename')->get();
        
        $this->app->smarty->assign(array(
            'NbTicket'       =>  $Tmp['nb'],
            'Lieux'          =>  $Lieux,
            'Categories'     =>  $Categories,
        ));
        
        return $this->app->smarty->fetch( VIEW_PATH . 'stats' . DS . 'stats.tpl');
        
    }
    
    public function ajaxGetNbTicketByLieuAction(){
        $Stats = array();
        $Date = array();
		$AnnuelStat = false;
		
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
        
		if( empty($Date['month']) ):
			$AnnuelStat = true;
		else:
			if( $Date['month'] < 10 ):
				$Date['month'] = '0'. $Date['month'];
			endif;
		endif;
        
        // Recuperation des lieux
        $Lieux = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        
        // boucles sur les lieux pour faire le cumul des tickets
        $i=0;
        foreach( $Lieux as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Tmp =  $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt');
				if( $AnnuelStat ):
					$this->app->db->where_free('ggt.date LIKE "'. $Date['year'] .'-%" AND ggt.locations_id = ' . $Row['id'] . ' ');
				else:
					$this->app->db->where_free('ggt.date LIKE "'. $Date['year'] .'-'. $Date['month'] .'-%" AND ggt.locations_id = ' . $Row['id'] . ' ');
				endif;
                        
                $Tmp = $this->app->db->get_one();
                
                $Stats[$i]['nombre'] = $Tmp['nb'];
                $Stats[$i]['lieu'] = $Row['completename'];
                
                $CumulTicket = $CumulTicket + $Tmp['nb'];

                $Stats[$i]['nbcollab'] = $this->registry->db->count('glpi_users', array('locations_id =' => $Row['id']));

                if( $Stats[$i]['nbcollab'] > 0 ){
                    $Stats[$i]['ratio'] = round($Stats[$i]['nombre']/$Stats[$i]['nbcollab'] ,2);
                }
                $i++;
            endif;
        endforeach;
        
        $this->app->smarty->assign(array(
           'Stats'          =>  $Stats,
           'CumulTickets'   =>  $CumulTicket,
        ));
        
        return $this->app->smarty->fetch( VIEW_PATH . 'stats' . DS . 'ajax_nb_ticket_par_lieu.tpl');
    }
    
    public function ajaxGetNbTicketByCategorieAction(){
        $Stats = array();
        $Date = array();
		$AnnuelStat = false;
		
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
        
		if( empty($Date['month']) ):
			$AnnuelStat = true;
		else:
			if( $Date['month'] < 10 ):
				$Date['month'] = '0'. $Date['month'];
			endif;
		endif;
        
        // Recuperation des lieux
        $Categories =	$this->app->db->select('id, completename, itilcategories_id, level')
						->from('glpi_itilcategories gtc')
						->order('completename')
						->get();
        
        // boucles sur les lieux pour faire le cumul des tickets
        $i=0;
        foreach( $Categories as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $this->app->db->select('COUNT(id) as nb')
					->from('glpi_tickets ggt');
			
				if( $AnnuelStat ):
					$this->app->db->where_free('ggt.date LIKE "'. $Date['year'] .'-%" AND ggt.itilcategories_id = ' . $Row['id'] . ' ');
				else:
					$this->app->db->where_free('ggt.date LIKE "'. $Date['year'] .'-'. $Date['month'] .'-%" AND ggt.itilcategories_id = ' . $Row['id'] . ' ');
				endif;
                        
                $Tmp = $this->app->db->get_one();
                
                $Stats[$i]['nombre'] = $Tmp['nb'];
                $Stats[$i]['categorie'] = $Row['completename'];
				$Stats[$i]['parent'] = $Row['itilcategories_id'];
				$Stats[$i]['level'] = $Row['level'];
				$Stats[$i]['cumul'] = ''; 
                $i++;
                $CumulTicket = $CumulTicket + $Tmp['nb'];
            endif;
        endforeach;
		
		$i=0;
		foreach( $Stats as $Row ):
			$Stats[$i]['cumul'] = $this->getCumul($Row['categorie'], $Stats);
			$i++;
		endforeach;
        
        $this->app->smarty->assign(array(
           'Stats'          =>  $Stats,
           'CumulTickets'   =>  $CumulTicket,
        ));
        
        return $this->app->smarty->fetch( VIEW_PATH . 'stats' . DS . 'ajax_nb_ticket_par_categorie.tpl');
    }
    
    public function generateFormFiltreAction(){

        // Recuperation des lieux
        if(!$Lieux = $this->registry->cache->get('LieuxForSelect')){
            // Requete recuperation des lieux
            $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();

            $Lieux = array();

            // On boucle sur les lieux pour supprimer les -
            foreach( $ResSql as $Row ):
                if( substr($Row['completename'],0,1) != '-'):
                    $Lieux[] = $Row;
                endif;
            endforeach;

            // Sauvegarde du cache
            $this->registry->cache->save(serialize($Lieux));
        }else{
            $Lieux = unserialize($Lieux);
        }

        // Recuperation des categories
        if(!$Categories = $this->registry->cache->get('categoriesForSelect')){
            $Categories = $this->app->db->select('id, completename')->from('glpi_itilcategories gtc')->order('completename')->get();
            $this->registry->cache->save(serialize($Categories));  
        }else{
            $Categories = unserialize($Categories);
        }
        
         $this->app->smarty->assign(array(
           'Lieux'          =>  $Lieux,
           'Categories'     =>  $Categories,
        ));
         
        return $this->app->smarty->fetch( VIEW_PATH . 'stats' . DS . 'ajax_form_filtre_full.tpl');
    }
    
    public function getGraphAction(){
        // Appel librairie
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pData.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pDraw.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pImage.class.php';
                
        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
		
		if( $Date['month'] < 10 ):
			$Date['month'] = '0'. $Date['month'];
		endif;
		
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        
        $i=0;
        $Lieux = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Lieux[$i] = $Row;
            endif;
            $i++;
        endforeach;
        
        $MyData = new pData();
        $LieuxData = array();
        // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-01 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-07 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "7");
            $LieuxData[] = $Lieu['completename'];
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 08 au 14
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-08 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-14 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "14");
            
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
        $Data = array();
            
            
            // 15 au 21
           $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-15 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-21 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "21");
            
        endforeach;
        
        // Boucle du 22 au 28
        foreach( $Lieux as $Lieu ):
        $Data = array();            
            
            // 22 au 28
           $Tmp  =  $this->app->db->select('COUNT(id) as nb')
                    ->from('glpi_tickets ggt')
                    ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-22 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-28 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                    ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "28");
            
        endforeach;
        
        $MyData->setAxisName(0,"Tickets");
        $MyData->addPoints( $LieuxData ,"Lieu");
        $MyData->setAbscissa("Lieu");
        
        /* Create the pChart object */
        $myPicture = new pImage(1500,400,$MyData);
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=> ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . "fonts" . DS .  "pf_arma_five.ttf","FontSize"=>6));
         
        /* Draw the scale  */
        $myPicture->setGraphArea(50,30,1400,280);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"LabelRotation"=>90));
         
        /* Turn on shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
         
        /* Draw the chart */
        $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
        $myPicture->drawBarChart($settings);
         
        /* Write the chart legend */
        $myPicture->drawLegend(0,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
        
        $myPicture->stroke();
    }
    
    public function getGraphPart1Action(){
        
        // Appel librairie
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pData.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pDraw.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pImage.class.php';
                
        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
        
        if( $Date['month'] < 10 ):
            $Date['month'] = '0'. $Date['month'];
        endif;
        
        // On compte le nb de lieux
        $nblieux = $this->app->db->count('glpi_locations');
        $limit = round($nblieux/2);
        
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->limit($limit)->get();
        
        $i=0;
        $Lieux = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Lieux[$i] = $Row;
            endif;
            $i++;
        endforeach;
        
        $MyData = new pData();
        $LieuxData = array();
        // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-01 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-07 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "7");
            $LieuxData[] = $Lieu['completename'];
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 08 au 14
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-08 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-14 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "14");
            
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
        $Data = array();
            
            
            // 15 au 21
           $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-15 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-21 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "21");
            
        endforeach;
        
        // Boucle du 22 au 28
        foreach( $Lieux as $Lieu ):
        $Data = array();            
            
            // 22 au 28
           $Tmp  =  $this->app->db->select('COUNT(id) as nb')
                    ->from('glpi_tickets ggt')
                    ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-22 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-28 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                    ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "28");
            
        endforeach;
        
        $MyData->setAxisName(0,"Tickets");
        $MyData->addPoints( $LieuxData ,"Lieu");
        $MyData->setAbscissa("Lieu");
        
        /* Create the pChart object */
        $myPicture = new pImage(1500,400,$MyData);
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=> ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . "fonts" . DS .  "pf_arma_five.ttf","FontSize"=>6));
         
        /* Draw the scale  */
        $myPicture->setGraphArea(50,30,1400,280);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"LabelRotation"=>90));
         
        /* Turn on shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
         
        /* Draw the chart */
        $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
        $myPicture->drawBarChart($settings);
         
        /* Write the chart legend */
        $myPicture->drawLegend(0,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
        
        $myPicture->stroke();
    }
    
    public function getGraphPart2Action(){
        // Appel librairie
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pData.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pDraw.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pImage.class.php';
                
        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
        
        if( $Date['month'] < 10 ):
            $Date['month'] = '0'. $Date['month'];
        endif;
        
        // On compte le nb de lieux
        $nblieux = $this->app->db->count('glpi_locations');
        $limit = round($nblieux/2);
        
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->limit($limit)->offset($limit)->get();
        
        $i=0;
        $Lieux = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Lieux[$i] = $Row;
            endif;
            $i++;
        endforeach;
        
        $MyData = new pData();
        $LieuxData = array();
        // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-01 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-07 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "7");
            $LieuxData[] = $Lieu['completename'];
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 08 au 14
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-08 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-14 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "14");
            
        endforeach;
        
         // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
        $Data = array();
            
            
            // 15 au 21
           $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-15 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-21 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "21");
            
        endforeach;
        
        // Boucle du 22 au 28
        foreach( $Lieux as $Lieu ):
        $Data = array();            
            
            // 22 au 28
           $Tmp  =  $this->app->db->select('COUNT(id) as nb')
                    ->from('glpi_tickets ggt')
                    ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-22 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-28 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                    ->get_one();
            $Data[] = $Tmp['nb'];
            $MyData->addPoints($Data, "28");
            
        endforeach;
        
        $MyData->setAxisName(0,"Tickets");
        $MyData->addPoints( $LieuxData ,"Lieu");
        $MyData->setAbscissa("Lieu");
        
        /* Create the pChart object */
        $myPicture = new pImage(1500,400,$MyData);
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=> ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . "fonts" . DS .  "pf_arma_five.ttf","FontSize"=>6));
         
        /* Draw the scale  */
        $myPicture->setGraphArea(50,30,1400,280);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"LabelRotation"=>90));
         
        /* Turn on shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
         
        /* Draw the chart */
        $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
        $myPicture->drawBarChart($settings);
         
        /* Write the chart legend */
        $myPicture->drawLegend(0,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
        
        $myPicture->stroke();
    }
	
	public function getGraphAnnuelFullAction(){
		// Appel librairie
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pData.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pDraw.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pImage.class.php';
                
        $Date = array();
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
		
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        
        $i=0;
        $Lieux = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Lieux[$i] = $Row;
            endif;
            $i++;
        endforeach;
        
        $MyData = new pData();
        $LieuxData = array();
        // On boucle sur les lieux
        foreach( $Lieux as $Lieu ):
            $Data = array();
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date LIKE "'. $Date['year'] .'-%" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "");
            $LieuxData[] = $Lieu['completename'];
        endforeach;    
        
        $MyData->setAxisName(0,"Tickets");
        $MyData->addPoints( $LieuxData ,"Lieu");
        $MyData->setAbscissa("Lieu");
        
        /* Create the pChart object */
        $myPicture = new pImage(1500,400,$MyData);
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=> ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . "fonts" . DS .  "pf_arma_five.ttf","FontSize"=>6));
         
        /* Draw the scale  */
        $myPicture->setGraphArea(50,30,1400,280);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"LabelRotation"=>90));
         
        /* Turn on shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
         
        /* Draw the chart */
        $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
        $myPicture->drawBarChart($settings);
         
        /* Write the chart legend */
        $myPicture->drawLegend(0,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
        
        $myPicture->stroke();
	}
	
	public function getGraphAnnuelFullCategorieAction(){
		// Appel librairie
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pData.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pDraw.class.php';
        require_once ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . 'class' . DS . 'pImage.class.php';
                
        $Date = array();
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;
		
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_itilcategories gl')->order('completename')->get();
        
        $i=0;
        $Categories = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Categories[$i] = $Row;
            endif;
            $i++;
        endforeach;
        
        $MyData = new pData();
        $CatData = array();
        // On boucle sur les lieux
        foreach( $Categories as $Cat ):
            $Data = array();
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date LIKE "'. $Date['year'] .'-%" AND ggt.itilcategories_id = ' . $Cat['id'] . ' ')
                        ->get_one();
            $Data[] = $Tmp['nb'];
            
            $MyData->addPoints($Data, "");
            $CatData[] = $Cat['completename'];
        endforeach;    
        
        $MyData->setAxisName(0,"Tickets");
        $MyData->addPoints( $CatData ,"Categories");
        $MyData->setAbscissa("Categories");
        
        /* Create the pChart object */
        $myPicture = new pImage(1500,400,$MyData);
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_VERTICAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>100));
        $myPicture->drawGradientArea(0,0,1500,400,DIRECTION_HORIZONTAL,array("StartR"=>240,"StartG"=>240,"StartB"=>240,"EndR"=>180,"EndG"=>180,"EndB"=>180,"Alpha"=>20));
        $myPicture->setFontProperties(array("FontName"=> ROOT_PATH . 'kernel' . DS . 'lib' . DS . 'pChart' . DS . "fonts" . DS .  "pf_arma_five.ttf","FontSize"=>6));
         
        /* Draw the scale  */
        $myPicture->setGraphArea(50,30,1400,280);
        $myPicture->drawScale(array("CycleBackground"=>TRUE,"DrawSubTicks"=>TRUE,"GridR"=>0,"GridG"=>0,"GridB"=>0,"GridAlpha"=>10,"LabelRotation"=>90));
         
        /* Turn on shadow computing */ 
        $myPicture->setShadow(TRUE,array("X"=>1,"Y"=>1,"R"=>0,"G"=>0,"B"=>0,"Alpha"=>10));
         
        /* Draw the chart */
        $settings = array("Floating0Serie"=>"Floating 0","Draw0Line"=>TRUE,"Gradient"=>TRUE, "DisplayPos"=>LABEL_POS_INSIDE,"DisplayValues"=>TRUE,"DisplayR"=>255, "DisplayG"=>255,"DisplayB"=>255,"DisplayShadow"=>TRUE,"Surrounding"=>10);
        $myPicture->drawBarChart($settings);
         
        /* Write the chart legend */
        $myPicture->drawLegend(0,12,array("Style"=>LEGEND_NOBORDER,"Mode"=>LEGEND_HORIZONTAL));
        
        $myPicture->stroke();
	}
	
	public function getCumul($complename, $Datas){
		$Cumul = 0;
		
		foreach($Datas as $Data):
			if( strpos($Data['categorie'], $complename) !== false):
				$Cumul = $Cumul + $Data['nombre'];
				
			endif;
			
		endforeach;
		
		return $Cumul;
	}

    public function getDataByLieuYear($year){
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        
        $i=1;
        $Lieux = array();
        foreach( $ResSql as $Row){
            if( substr($Row['completename'],0,1) != '-'){
                $Lieux[$i] = $Row;
            }
            $i++;
        }

         $Stats = array();
         $i=1;
         $y=0;
        foreach( $Lieux as $Lieu ){
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date LIKE "'. $year .'%" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            
            $Stats[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        }            
            
      return json_encode(array($Stats), JSON_NUMERIC_CHECK );

    }
	
    /**
     *  Recupere et format les donnees pour la generation du graph
     *  @return json
     */
    public function ajaxGraphiByLieuAction(){

        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;

        if( !isset($_GET['month']) ){
            return $this->getDataByLieuYear($Date['year']);
        }
        
        if( $Date['month'] < 10 ){
            $Date['month'] = '0'. $Date['month'];
        }
        
        // Recuperation des Lieux
        $ResSql = $this->app->db->select('id, completename')->from('glpi_locations gl')->order('completename')->get();
        
        $i=1;
        $Lieux = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $Lieux[$i] = $Row;
            endif;
            $i++;
        endforeach;

        // On boucle sur les lieux
         $Semaine1 = array();
         $i=1;
         $y=0;
        foreach( $Lieux as $Lieu ):            
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-01 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-07 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            
            $Semaine1[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 2 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine2 = array();
        foreach( $Lieux as $Lieu ): 
            // 08 au 14
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-08 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-14 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Semaine2[$y] = array($Tmp['nb'],$i);
            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 3 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine3 = array();
        foreach( $Lieux as $Lieu ): 

             $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-15 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-21 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                        ->get_one();
            $Semaine3[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 4 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine4 = array();
        foreach( $Lieux as $Lieu ): 

            $Tmp  =  $this->app->db->select('COUNT(id) as nb')
                    ->from('glpi_tickets ggt')
                    ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-22 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-28 23:59:59" AND ggt.locations_id = ' . $Lieu['id'] . ' ')
                    ->get_one();
            $Semaine4[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;
        
        return json_encode(array($Semaine1, $Semaine2, $Semaine3, $Semaine4), JSON_NUMERIC_CHECK );
    }

    public function getDataByCategoriesYear($year){
        // Recuperation des categories
        $ResSql = $this->app->db->select('id, completename')->from('glpi_itilcategories')->order('completename')->get();
        
        $i=1;
        $categories = array();
        foreach( $ResSql as $Row){
            if( substr($Row['completename'],0,1) != '-'){
                $categories[$i] = $Row; 
            }
            $i++;  
        }
            

        // On boucle sur les categories
        $Stats = array();
        $i=1;
        $y=0;

        foreach( $categories as $categorie ){
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date LIKE "'. $year .'%" AND ggt.itilcategories_id = ' . $categorie['id'] . ' ')
                        ->get_one();
            
            $Stats[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++; 
        }           
 
      return json_encode(array($Stats), JSON_NUMERIC_CHECK );

    } 

     /**
     *  Recupere et format les donnees pour la generation du graph
     *  @return json
     */
    public function ajaxGraphiByCategorieAction(){

        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        $CumulTicket = 0;

        if( !isset($_GET['month']) ){
            return $this->getDataByCategoriesYear($Date['year']);
        }
        
        if( $Date['month'] < 10 ){
            $Date['month'] = '0'. $Date['month'];
        }
        
        // Recuperation des categories
        $ResSql = $this->app->db->select('id, completename')->from('glpi_itilcategories')->order('completename')->get();
        
        $i=1;
        $categories = array();
        foreach( $ResSql as $Row):
            if( substr($Row['completename'],0,1) != '-'):
                $categories[$i] = $Row;
            endif;
            $i++;
        endforeach;

        // On boucle sur les lieux
         $Semaine1 = array();
         $i=1;
         $y=0;
        foreach( $categories as $categorie ):            
            
            // 01 au 07
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-01 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-07 23:59:59" AND ggt.itilcategories_id = ' . $categorie['id'] . ' ')
                        ->get_one();
            
            $Semaine1[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 2 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine2 = array();
        foreach( $categories as $categorie ): 
            // 08 au 14
            $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-08 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-14 23:59:59" AND ggt.itilcategories_id = ' . $categorie['id']  . ' ')
                        ->get_one();
            $Semaine2[$y] = array($Tmp['nb'],$i);
            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 3 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine3 = array();
        foreach( $categories as $categorie ): 

             $Tmp  =    $this->app->db->select('COUNT(id) as nb')
                        ->from('glpi_tickets ggt')
                        ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-15 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-21 23:59:59" AND ggt.itilcategories_id = ' . $categorie['id']  . ' ')
                        ->get_one();
            $Semaine3[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;

        ///////////////
        // Semaine 4 //
        // ////////////
        $i=1;
        $y=0;
        $Semaine4 = array();
        foreach( $categories as $categorie ): 

            $Tmp  =  $this->app->db->select('COUNT(id) as nb')
                    ->from('glpi_tickets ggt')
                    ->where_free('ggt.date >= "'. $Date['year'] .'-'. $Date['month'] .'-22 00:00:00" AND ggt.date <= "'. $Date['year'] .'-'. $Date['month'] .'-28 23:59:59" AND ggt.itilcategories_id = ' . $categorie['id'] . ' ')
                    ->get_one();
            $Semaine4[$y] = array($Tmp['nb'],$i);

            $i++;
            $y++;
        endforeach;
        
        return json_encode(array($Semaine1, $Semaine2, $Semaine3, $Semaine4), JSON_NUMERIC_CHECK );
    }

    public function ajaxGetCategoriesAction(){
        $categories = array();

        $ResSql = $this->app->db->select('completename')->from('glpi_itilcategories')->order('completename')->get();

        foreach ($ResSql as $categorie) {
            
            if( substr($categorie['completename'],0,1) != '-'){
                array_push($categories, $categorie['completename']);
            }            
        }

        return json_encode($categories);
    }

    /**
     * Recupere la liste des liens et l'envoie au format json
     * Utiliser pour le graph Bar
     * @return [type] [description]
     */
    public function ajaxGetLieuxAction(){
        $Lieux = array();

        $ResSql = $this->app->db->select('completename')->from('glpi_locations gl')->order('completename')->get();

        foreach ($ResSql as $Lieu) {
            
            if( substr($Lieu['completename'],0,1) != '-'){
                array_push($Lieux, $Lieu['completename']);
            }            
        }

        return json_encode($Lieux);
    }

    /**
     * Recupere le nombre incident et demande pour graph
     * @return json [description]
     */
    public function ajaxGraphByTypeAction(){

        $Date = array();
        $Date['month'] = $this->app->HTTPRequest->getData('month');
        $Date['year'] = $this->app->HTTPRequest->getData('year');
        
        if( $Date['month'] < 10 ){
            $Date['month'] = '0'. $Date['month'];
        }

        $Where = array();
        if( !empty($Data['month']) ){
            $Where['date >='] = $Date['year'] .'-'. $Date['month'] .'-01 00:00:00"';
            $Where['date <='] = $Date['year'] .'-'. $Date['month'] .'-31 23:59:59"';

            $NbIncidents = $this->registry->db->count('glpi_tickets', array_merge($Where, array('type =' => 1)));
            $NbDemandes = $this->registry->db->count('glpi_tickets', array_merge($Where, array('type =' => 2))); 
        }else{
            $Where['date LIKE '] = $Date['year'] .'-%';

            $NbIncidents = $this->registry->db->count('glpi_tickets', array_merge($Where, array('type =' => 1)));
            $NbDemandes = $this->registry->db->count('glpi_tickets', array_merge($Where, array('type =' => 2)));
        }
        

        return json_encode(array( array('Incidents', $NbIncidents), array('Demandes', $NbDemandes)), JSON_NUMERIC_CHECK);
    }

    /**
     * Recupere le nombre de source de demande
     * @return json_encode [description]
     */
    public function ajaxGraphByRequestAction(){

       
            $Date = array();
            $Date['month'] = $this->app->HTTPRequest->getData('month');
            $Date['year'] = $this->app->HTTPRequest->getData('year');
            
            if( $Date['month'] < 10 ){
                $Date['month'] = '0'. $Date['month'];
            }

            $Where = array();
            if( !empty($Date['month']) ){
                $Where['date >='] = $Date['year'] .'-'. $Date['month'] .'-01 00:00:00"';
                $Where['date <='] = $Date['year'] .'-'. $Date['month'] .'-31 23:59:59"'; 
            }else{
                $Where['date LIKE '] = $Date['year'] .'-%';
            }
            

            // Recuperations des types
            $Types = $this->registry->db->get('glpi_requesttypes');

            $Data = array();

            // On boucles sur les Types
            foreach($Types as $Type){
                $Qte = $this->registry->db->count('glpi_tickets', array_merge($Where, array('requesttypes_id =' => $Type['id'])));
                $Data[] = array($Type['name'], $Qte);
            }

            // Envoie du resultat JSON
            return json_encode($Data, JSON_NUMERIC_CHECK);    

        
    }

    /**
     * Recupere les tickets ouvert/resolu/clos dans le mois
     * @return [type] [description]
     */
    public function ajaxGraphByStatusAction(){

        if( isset($_GET['month']) && !empty($_GET['month']) ){
        
            $Date = array(
                'month' => $this->registry->HTTPRequest->getData('month'),
                'year' => $this->registry->HTTPRequest->getData('year')
            );

            if( !empty($Date['month']) ){
                if( $Date['month'] < 10 ){
                    $Date['month'] = '0'. $Date['month'];
                }

                // Recuperation par jour du mois
                $nb_jours = date('t', mktime(1,1,1, $Date['month'],1,$Date['year']));

                $Data = array();
                $Data2 = array();
                $Data3 = array();

                    // Ticket ouverts
                    $y = 0;
                    for ($i2=1; $i2 <= $nb_jours; $i2++) { 
                       $jour = $i2;
                       if($jour < 10 ){
                        $jour = '0'. $jour;
                       }
                       $Data[] = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $Date['month'] . '-' . $jour .'%') );
                    }

                    for ($i2=1; $i2 <= $nb_jours; $i2++) { 
                        $jour = $i2;
                       if($jour < 10 ){
                        $jour = '0'. $jour;
                       }
                       $Data2[] = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $Date['month'] . '-' . $jour .'%', 'status =' => 'solved') );
                    }

                    for ($i2=1; $i2 <= $nb_jours; $i2++) { 
                        $jour = $i2;
                       if($jour < 10 ){
                        $jour = '0'. $jour;
                       }
                       $Data3[] = $NbTicketClosed = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $Date['month'] . '-' . $jour .'%', 'status =' => 'closed') );
                    }
            }

            return json_encode(array($Data, $Data2, $Data3), JSON_NUMERIC_CHECK );
        }else{
            // Stats annuel

            $Date = array('year' => $this->registry->HTTPRequest->getData('year'));

            $Data = array();
            $Data2 = array();
            $Data3 = array();

            // Ticket ouverts
            for ($i2=1; $i2 <=12; $i2++) { 
               $mois = $i2;
               if($mois < 10 ){ $mois = '0'. $mois; }
               $Data[] = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $mois . '%') );
            }

            for ($i2=1; $i2 <= 12; $i2++) { 
               $mois = $i2;
               if($mois < 10 ){ $mois = '0'. $mois; }
               $Data2[] = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $mois . '%', 'status =' => 'solved') );
            }

            for ($i2=1; $i2 <= 12; $i2++) { 
               $mois = $i2;
               if($mois < 10 ){ $mois = '0'. $mois; }
               $Data3[] = $this->registry->db->count('glpi_tickets', array('date LIKE ' => $Date['year'] . '-' . $mois . '%', 'status =' => 'closed') );
            }

            return json_encode(array($Data, $Data2, $Data3), JSON_NUMERIC_CHECK );
        }

    }

    public function ajaxTopTenSiteAction(){

        $Date = array(
            'month' => $this->registry->HTTPRequest->getData('month'),
            'year' => $this->registry->HTTPRequest->getData('year')
        );

        if( !empty($Date['month']) ){
            if( $Date['month'] < 10 ){
                $Date['month'] = '0'. $Date['month'];
                $where = array('date LIKE ' => $Date['year'] . '-' . $Date['month'] . '-%', 'gt.is_deleted =' => 0);
            }
        }else{
            $where = array('date LIKE ' => $Date['year'] . '-%', 'gt.is_deleted =' => 0);
        }

        // Requete
        $Result =   $this->registry->db->select('COUNT(gt.id) as nb_tickets, gl.completename as site')
                    ->from('glpi_tickets gt')
                    ->left_join('glpi_locations gl','gt.locations_id = gl.id')
                    ->where($where)
                    ->group_by('gt.locations_id')
                    ->order('nb_tickets DESC')
                    ->limit(10)
                    ->offset(0)
                    ->get();
        
        return json_encode($Result);

    }

    public function ajaxTopTenCategorieAction(){

        $Date = array(
            'month' => $this->registry->HTTPRequest->getData('month'),
            'year' => $this->registry->HTTPRequest->getData('year')
        );

        if( !empty($Date['month']) ){
            if( $Date['month'] < 10 ){
                $Date['month'] = '0'. $Date['month'];
                $where = array('date LIKE ' => $Date['year'] . '-' . $Date['month'] . '-%', 'gt.is_deleted =' => 0);
            }
        }else{
            $where = array('date LIKE ' => $Date['year'] . '-%', 'gt.is_deleted =' => 0);
        }

        // Requete
        $Result =   $this->registry->db->select('COUNT(gt.id) as nb_tickets, gi.completename as categorie')
                    ->from('glpi_tickets gt')
                    ->left_join('glpi_itilcategories gi','gt.itilcategories_id = gi.id')
                    ->where($where)
                    ->group_by('gt.itilcategories_id')
                    ->order('nb_tickets DESC')
                    ->limit(10)
                    ->offset(0)
                    ->get();
        
        return json_encode($Result);

    }

    public function compareAction(){

        // Tableau qui va contenir les intervalles de dates
        $date = array(
            'start1'    =>  $this->registry->HTTPRequest->postData('start1'),
            'end1'      =>  $this->registry->HTTPRequest->postData('end1'),
            'start2'    =>  $this->registry->HTTPRequest->postData('start2'),
            'end2'      =>  $this->registry->HTTPRequest->postData('end2'),
        );

        $this->load_manager('lieu');
        $lieux = $this->manager->lieu->getAll();

        $i=0;
        foreach($lieux as $row){
            $nbcollab = $this->registry->db->count('glpi_users', array('locations_id =' => $row['id']));
            if($nbcollab==0){
                $i++;
                continue;
            }
            $lieux[$i]['nbcollab'] = $nbcollab;
            $lieux[$i]['nbticket_d1'] = $this->registry->db->count('glpi_tickets', array('date >=' => $date['start1'] . ' 00:00:00', 'date <=' => $date['end1'] . ' 23:59:59', 'locations_id =' => $row['id'] ) );
            $lieux[$i]['nbticket_d2'] = $this->registry->db->count('glpi_tickets', array('date >=' => $date['start2'] . ' 00:00:00', 'date <=' => $date['end2'] . ' 23:59:59', 'locations_id =' => $row['id'] ) );
            $lieux[$i]['ratio_d1'] = round($lieux[$i]['nbticket_d1']/$nbcollab,2);
            $lieux[$i]['ratio_d2'] = round($lieux[$i]['nbticket_d2']/$nbcollab,2);
            $i++;
        }

       // echo "<pre>"; print_r($lieux);

        // On verifie que la page est appel par formulaire
        /*if( $this->registry->HTTPRequest->callMethod() != 'POST' ){
            return $this->indexAction();
        }*/

        
/*
        $date['start1'] = '2013-03-01';
        $date['end1'] = '2013-03-09';
        $date['start2'] = '2013-04-01';
        $date['end2'] = '2013-04-09';
*/
        // Verification du format des dates
        

 
        $stats = array(
            'date1' => array(
                'nbticket'  =>  $this->registry->db->count('glpi_tickets', array('date >=' => $date['start1'] . ' 00:00:00', 'date <=' => $date['end1'] . ' 23:59:59' ) ),
                'nbclose'   =>  $this->registry->db->count('glpi_tickets', array('closedate >=' => $date['start1'] . ' 00:00:00', 'closedate <=' => $date['end1'] . ' 23:59:59', 'status =' => 'closed' ) ),
                'nbsolved'   =>  $this->registry->db->count('glpi_tickets', array('solvedate >=' => $date['start1'] . ' 00:00:00', 'solvedate <=' => $date['end1'] . ' 23:59:59', 'status =' => 'solved' ) ),
            ),
            'date2' => array(
                'nbticket'  =>  $this->registry->db->count('glpi_tickets', array('date >=' => $date['start2'] . ' 00:00:00', 'date <=' => $date['end2'] . ' 23:59:59' ) ),
                'nbclose'   =>  $this->registry->db->count('glpi_tickets', array('closedate >=' => $date['start2'] . ' 00:00:00', 'closedate <=' => $date['end2'] . ' 23:59:59', 'status =' => 'closed' ) ),
                'nbsolved'   =>  $this->registry->db->count('glpi_tickets', array('solvedate >=' => $date['start2'] . ' 00:00:00', 'solvedate <=' => $date['end2'] . ' 23:59:59', 'status =' => 'solved' ) ),
            ),
            
        );
    
        $stats['compare'] = array(
            'nbticket'  =>  $stats['date2']['nbticket'] - $stats['date1']['nbticket'],
            'nbclose'   =>  $stats['date2']['nbclose'] - $stats['date1']['nbclose'],
            'nbsolved'  =>  $stats['date2']['nbsolved'] - $stats['date1']['nbsolved'],
        );

        $this->registry->smarty->assign(array(
            'stats' =>  $stats,
            'date'  =>  $date,
            'lieux' =>  $lieux,
        ));

        return $this->registry->smarty->fetch(VIEW_PATH . 'stats' . DS . 'compare.tpl');
        echo"<br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>";
        var_dump($stats);
    }

}