<?php

class Ep_Portfolio_Manage extends Ep_Db_Identifier
{
    public function WelcomeMessage(){
        {
            $userListQuery = " select identifier as ID,first_name as FirstName, last_name as LastName, address as Address,city as City,state as State,zipcode as Zipcode,country as Country,phone_number as PhoneNumber,
			login as UserName, email as EmailId ,status as Status, type as Type
			from User inner join UserPlus on User.identifier = UserPlus.user_id limit 2";
            $userListResult = $this->getQuery($userListQuery,true);
            return $userListResult;
        }
    }
}