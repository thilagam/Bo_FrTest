<?php
/* *
 * Ep_User_ProfileChangeLog
 * @author Admin
 * @package ProfileChangeLog
 * @version 1.0
 */
class Ep_User_ProfileChangeLog extends Ep_Db_Identifier
{
	protected $_name = 'ProfileChangeLog';
    private $identifier;
	private $user_id;
	private $old_email;
    private $new_email;
	private $changed_by;
	private $created_at;

	public function loadData($array)
	{
        $this->identifier=$array["identifier"] ;
        $this->user_id=$array["user_id"] ;
        $this->old_email=$array["old_email"];
        $this->new_email=$array["new_email"];
        $this->changed_by=$array["changed_by"];
        $this->created_at=$array["created_at"];
		return $this;
	}
	public function loadintoArray()
	{
		$array = array();
        $array["identifier"] = $this->getIdentifier();
        $array["user_id"] = $this->user_id;
        $array["old_email"] = $this->old_email;
        $array["new_email"] = $this->new_email;
        $array["changed_by"] = $this->changed_by;
        $array["created_at"] = $this->created_at;
		return $array;
	}
    public function __set($name, $value) {
            $this->$name = $value;
    }
    public function __get($name){
            return $this->$name;
    }


}
