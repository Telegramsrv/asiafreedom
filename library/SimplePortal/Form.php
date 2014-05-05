<?php

abstract class SimplePortal_Form{



	abstract function getFields();


	protected $_input;

	public function __construct(XenForo_Input $input){
		$this->_input = $input;
		return $this;
	}

	public function getValidatedInputFields(){
		return $this->_input->filter($this->getFields());
	}

	public $dataWriter = null;

	public function insert(){
		$dw = XenForo_DataWriter::create($this->dataWriter);
		$dw->bulkSet($this->getValidatedInputFields());
		$dw->save();
		return $dw->getMergedData();
	}
}