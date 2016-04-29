<?php

class Ep_Ao_Data extends Ep_Db_Identifier
{
	public function getClientAo()
	{
		$query1="SELECT u.identifier, u.email
					FROM User u
					INNER JOIN Delivery d ON u.identifier = d.user_id
					INNER JOIN Article a ON d.id = a.delivery_id
					INNER JOIN Participation p ON a.id = p.article_id
					WHERE u.type = 'client' AND d.premium_total = '0' AND p.status = 'published'
					GROUP BY u.identifier";
		$result1 = $this->getQuery($query1,true);
        return $result1;
    }
}	