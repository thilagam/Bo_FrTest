<?php
class SenddocHTML
{
private $ctrlobj;
private $sendObject;
private $rsession;

public function __construct($cbj, $action, $gid, $arrayDb, $lang){
	$this->ctrlobj=$cbj;
	$this->_arrayDb = $arrayDb;
	$this->userSession = Zend_Registry::get('userSession');
	$this->_lang = $lang;
	$this->godfatherid = $gid;
	$this->action = $action;
}
public function rightsenddoc()
	{
		
		/*$_composedSend = $this->userSession->composedSend;
		if(!isset($_composedSend))
		{
			$_composedSend = new Ep_Sending_ComposedSend();
		}*/
		//$action = $this->_request->getParam("type");
		//$godfatherId = $this->_request->getParam("godfatherId");
		$cp = new Ep_Customer_CustomerPlus();
		if(!$cp->getrecordbyIdentifier($this->userSession->identifier))
		{
			$testCust = false;
		}
		else
		{
			$testCust = true;
			$sending = new Ep_Sending_Sending();
			$sending->setCustomerId($this->userSession->identifier);
			$arrayS = $sending->selectAll();
			if(!count($arrayS))$testCode = true;
		}

		if($this->action =="applyCode")
		{
			$this->userSession->customerplus['godfatherId'] = $this->godfatherid;
			$this->userSession->godfatherId = $this->godfatherid;
		}

		$this->god = new Ep_Sending_Godfather("GODFATHER");
		if($this->god->loadByCode($this->godfatherid )){
			$this->sp1 = new Ep_Sending_SpOperation("SPOPERATION",$this->god->getSpOperationId());
		}
		else
		$this->sp1 = new Ep_Sending_SpOperation("SPOPERATION","xxxxxxxxxxxxxxx");

		//opération en cours
		$this->sp = new Ep_Sending_SpOperation("SPOPERATION");

		//si opération correspondante en cours
		if($this->sp1->enable())
		{
			$typeOperation = 1;
		}
		else{
		$typeOperation = 0;
		$this->userSession->godfatherId="";
		}

		$this->ctrlobj->remunerationTypeCust = $remunerationTypeCust  = $this->_arrayDb->loadArrayv2("remunerationTypeCust", $this->_lang);
		$this->ctrlobj->remunerationTypeCat = $remunerationTypeCat = $this->_arrayDb->loadArrayv2("remunerationTypeCat", $this->_lang);
		$this->ctrlobj->remunerationType = $this->_arrayDb->loadArrayv2("remunerationType", $this->_lang);
		$this->ctrlobj->remuneration = $this->_arrayDb->loadArrayv2("remuneration", $this->_lang);
		$remunerationTypeSpecial = $this->_arrayDb->loadArrayv2("remunerationTypeSpecial", $this->_lang);
		$activeCession = $this->_arrayDb->loadArrayv2("activeCession", $this->_lang);
		$this->ctrlobj->textes = $this->_arrayDb->loadArrayv2("textes", $this->_lang);
		$this->ctrlobj->fixed = $this->_arrayDb->loadArrayv2("fixed", $this->_lang);

		$typeCessionArray = array_keys($remunerationTypeCat,0);
		$typeCessionOperation = array_keys($remunerationTypeSpecial,$typeOperation);
		$typeCession = array_intersect($typeCessionArray,$typeCessionOperation);
		$typeCession = array_intersect($activeCession,$typeCession);
		$this->ctrlobj->typeCession = $typeCession;
		//print_r($typeCession);
		$this->ctrlobj->testCust			= $testCust;
		$this->ctrlobj->typeOperation		= $typeOperation;

		$this->ctrlobj->godfather_id		= $this->godfatherid;
		$this->ctrlobj->ambassedor_code_db = $this->userSession->customerplus['godfatherId'];
		$this->ctrlobj->tc				= $fixed[$tc];
		$this->ctrlobj->tc1				= $tc;
		$this->ctrlobj->spenable			= $this->sp->enable();
		$this->ctrlobj->testChecked		= false;
		$this->ctrlobj->composedSend		= $_composedSend;
		//$this->ctrlobj->cartType = "search";
	}

}
