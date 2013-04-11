<?php

class ticketManager extends BaseModel{
	
	public function getByLieuId($lid, $order = 't.id DESC'){
		
		//Cas lid = 72 : tours
		if( $lid == 72 )
			$where = 'tu.type = 1 AND (u.locations_id = 92 OR u.locations_id = 93)';
		else
			$where = 'tu.type = 1 AND u.locations_id = ' . $lid . ' ';
			
	
		return 	$this->db->select('t.*, u.name AS uname, u.realname, u.firstname, u.id as uid, tc.completename AS categorie')
				->from('glpi_tickets t')
				->left_join('glpi_tickets_users tu', 'tu.tickets_id = t.id')
				->left_join('glpi_users u', 'u.id = tu.users_id')
				->left_join('glpi_locations l', 'u.locations_id = l.id')
				->left_join('glpi_itilcategories tc', ' t.itilcategories_id = tc.id')
				->where_free( $where )
				->where_free('t.closedate IS NULL')
				->where_free('t.is_deleted = 0')
				->order($order)
				->get();	
	}
	
	public function getAll(){
		$this->db->select('t.*, u.locations_id AS lieu, tc.completename AS categorie')
			->from('glpi_tickets t')
			->left_join('glpi_tickets_users tu', 'tu.tickets_id = t.id')
			->left_join('glpi_users u', 'u.id = tu.users_id')
			->left_join('glpi_locations l', 'u.locations_id = l.id')
			->left_join('glpi_ticketcategories tc', ' t.ticketcategories_id = tc.id')
			->where( array('tu.type =' => 1) );
		
		return $this->db->get();
	}
	
	public function updateById( $data ){
		return $this->db->update('glpi_tickets', $data);
	}
}