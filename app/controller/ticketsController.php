<?php
/**
 * @author : Drouche Romain
 * @email : roumain18@gmail.com
 * @description : Permet le tri des tickets par lieu
 */

class ticketsController extends Controller{
	
	/**
	 * Affiche la liste des lieu
	 * @return string code HTML de la page
	 */
	public function indexAction(){
		$this->load_manager('lieu');
		
		$this->registry->smarty->assign('Lieux', $this->manager->lieu->getAll() );
		
		//$this->registry->addJS('jquery-1.6.2.min.js');
		// Affichage de la page	
		return $this->registry->smarty->fetch(VIEW_PATH . 'tickets' . DS . 'index.tpl');	
	}
	
	public function getByLieuAction(){
        if( $this->registry->HTTPRequest->getExists('order') )
            $orderby = $this->registry->HTTPRequest->getData('order');
        else
            $orderby = 't.id DESC';
        
		$lieu_id = $this->registry->HTTPRequest->getData('lieuid');
		$this->load_manager('ticket');
		$Tickets = $this->manager->ticket->getByLieuId( $lieu_id, $orderby );
		$this->registry->smarty->assign('Tickets', $Tickets );
		return $this->registry->smarty->fetch(VIEW_PATH . 'tickets' . DS . 'tab_tickets.tpl');
	}
	
	public function updateTicketAction(){
		$this->load_manager('ticket');
		$Tickets = $this->manager->ticket->getAll();
		
		$NbTicket = count($Tickets);
		$i=0;
		foreach( $Tickets as $Ticket):
			$this->manager->ticket->updateById( array('id' => $Ticket['id'], 'locations_id' => $Ticket['lieu']) );
			$i++;
		endforeach;
		
	}
}