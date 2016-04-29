<?php

class Ep_Ao_Favouritecontributor extends Ep_Db_Identifier
{
	protected $_name = 'Favourite_contributor';
	
	public function addfavcontrib($contrib,$client)
	{
			$chkQuery="SELECT * FROM ".$this->_name." WHERE client_id='".$client."' AND contrib_id='".$contrib."'";
			
			if(($resultfav = $this->getQuery($chkQuery,true)) != NULL)
				{ 
					$wherefav="client_id='".$client."' AND contrib_id='".$contrib."' ";
			
					$remfav=array();
					$remfav["status"]=1;
					$this->updateQuery($remfav,$wherefav);
				}
			else
				{ 
					$adfav=array();
					$adfav["contrib_id"]=$contrib;
					$adfav["client_id"]=$client;
					$this->insertQuery($adfav);
				}
	}
}	