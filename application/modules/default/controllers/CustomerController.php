<?php

class Default_CustomerController extends Zend_Controller_Action
{
	public function init(){

	}

	public function indexAction(){

	}
	public function addAction(){
		$this->view->headTitle("Nandan Selections - Add Customer");
	}
	public function postAction(){
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		$params = $this->_getAllParams();
		$db = Zend_Db_Table::getDefaultAdapter();
		$data = array(
			"name" => $params['name'],
			"contact_no" => $params['contact_no'],
			"address" => $params['address']
			);
		try{
			$db->insert("customers",$data);
		}
		catch(Exception $e){
			switch(get_class($e)){
				case 'Zend_Argument_Exception': $message = 'Argument Error.'; break;
				case 'Zend_Db_Statment_Exception': $message = 'Database Error.'; break;
				case 'default': $message = "Unknown Error."; break;
			}
			print_r($e);
			exit;
		}
		$last_insert_id = $db->lastInsertId();
		$balance = $params['balance'];
		$data = array(
			"customer_id" => $last_insert_id,
			"balance" => $balance
		);
		$date = date("Y-m-d");
		$trans_data = array(
			"desc" => "Initial Balance",
			"date" => $date,
			"customer_id" => $last_insert_id,
			"transaction_type" => 2,
			"amount" => $balance,
			"balance" => $balance
		);
		try{
			$db->insert("balance",$data);
			$db->insert("transactions",$trans_data);
		}
		catch(Exception $e){
			switch(get_class($e)){
				case 'Zend_Argument_Exception': $message = 'Argument Error.'; break;
				case 'Zend_Db_Statment_Exception': $message = 'Database Error.'; break;
				case 'default': $message = "Unknown Error."; break;
			}
			print_r($e);
			exit;
		}
		echo "TRUE";
	}
	public function getAllAction(){
		header('Content-Type: application/json');
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					 ->from("customers");
		try{
			$result = $db->query($select)->fetchAll();
		}
		catch(Exception $e){

		}
		echo json_encode($result);
	}
	public function viewAction(){
		$this->view->headTitle("Nandan Selections - View Customer Details");

	}
	public function testAction(){
		header('Content-Type: application/json');
		$data = $this->_getAllParams();
		$customer_id = $data['id'];
		
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(TRUE);
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select()
					 ->from("transactions")
					 ->where("customer_id = $customer_id");
		$result = $db->query($select)->fetchAll();
		$select1 = $db->select()
					  ->from("balance")
					  ->where("customer_id = $customer_id");
		$balance_array = $db->query($select1)->fetchAll();
		$balance = $balance_array[0]['balance'];
		$response = array(
			"data" => "",
			"balance"=>"$balance"
			);	
		$array = array();
		foreach($result as $a){
			$a['action'] = "<a href='/billing/edit?id=".$a['id'].".'>Edit</a>&nbsp;|&nbsp;<a href='/billing/delete?id=".$a['id']."'>Delete</a>";
			$a['balance'] = "Rs. ".$a['balance'];
			if($a['transaction_type'] == 1){
				$a['transaction_type'] = "Debit";
				$amount = $a['amount'];
				$a['amount'] = "<span style='color: green;'> + Rs. " . $a['amount'];
			}
			else if($a['transaction_type'] == 2){
				$a['transaction_type'] = "Credit";
				$amount = $a['amount'];
				$a['amount'] = "<span style='color: red;'> - Rs. " . $a['amount'];
			}
			array_push($array,$a);
		}
		$response["data"] = $array;
		echo json_encode($response);
	}
}