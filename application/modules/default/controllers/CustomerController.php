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
			switch(get_class($exception)){
				case 'Zend_Argument_Exception': $message = 'Argument Error.'; break;
				case 'Zend_Db_Statment_Exception': $message = 'Database Error.'; break;
				case 'default': $message = "Unknown Error."; break;
			}
		}
		$last_insert_id = $db->lastInsertId();
		$balance = $params['balance'];
		$data = array(
			"customer_id" => $last_insert_id,
			"balance" => $balance
		);
		try{
			$db->insert("balance",$data);
		}
		catch(Exception $e){
			switch(get_class($exception)){
				case 'Zend_Argument_Exception': $message = 'Argument Error.'; break;
				case 'Zend_Db_Statment_Exception': $message = 'Database Error.'; break;
				case 'default': $message = "Unknown Error."; break;
			}
		}
		echo "Customer successfully created!";
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
}