<?php
/**
 * Ep_User_User
 * @author admin
 * @package Ticket
 * @version 1.0
 */
 /*Status
        0 => sent by sender / received by recipient
        1 => received by sender / sent by recipient
        2 => classified by sender
        3 => classified by recipient
*/
class Ep_Kt_User extends Ep_Db_Identifier
{
    public function WelcomeMessage(){
        echo "yes welocme";
    }
}