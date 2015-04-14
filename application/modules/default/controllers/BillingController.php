<?php

class Default_BillingController extends Zend_Controller_Action
{
	public function init(){

	}	
	public function indexAction(){
		/*Default index action*/
		$this->view->headTitle("Nandan Selections - Billing");

		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					 ->from("transaction_type");
		try{
			$result = $db->query($select)->fetchAll();
		}
		catch(Exception $e){

		}
		$transaction_type = "<option value = 0 selected>Select a Transaction Type</option>";
		foreach($result as $e){
			$transaction_type .= "<option value = ".$e['id'].">".$e['desc']."</option>";
		}
		$this->view->select = $transaction_type;
	}
	public function postAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		$params = $this->_getAllParams();
		$db = Zend_Db_Table::getDefaultAdapter();
		$data = array(
			"desc" => $params['desc'],
			"transaction_type" => $params['transaction_type'],
			"amount" => $params['amount'],
			"customer_id" => $params['customer_id'],
			"date" => $params['date']
		);
		try{
			$db->insert("transactions",$data);
			if($params['transaction_type'] == 1)
				$update_string = "balance - " . $params['amount'];
			else if($params['transaction_type'] == 2)
				$update_string = "balance + " . $params['amount'];
			else 
				return;
			$update_data = array(
				"balance" => new Zend_Db_Expr($update_string)
			);
			$where = array(
				"customer_id = ?" => $params['customer_id']
			);
			$db->update("balance",$update_data,$where);
		}
		catch(Exception $e){
			print_r($e);
		}
		echo "Successful";
	}
}