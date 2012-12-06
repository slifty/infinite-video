<?php
###
# Info:
#  Last Updated 2011
#  Daniel Schultz
#
###
require_once("DbConn.php");

abstract class FactoryObject {
	
	# Constants
	// Initialization Types
	const INIT_EMPTY = -1;
	const INIT_DEFAULT = 0;
	
	// Limit Types
	const LIMIT_BEGINNING = 0;
	const LIMIT_ALL = NULL;
	
	
	
	# Instance Variables
	protected $itemId = 0;
	
	
	# Object Methods
	public function __construct($itemId = FactoryObject::INIT_EMPTY) {
		$data_arrays = static::gatherData((int)$itemId);
		$this->load($data_arrays[0]);
	}
	
	
	# FactoryObject Methods
	abstract protected static function gatherData($objectString, $start=FactoryObject::LIMIT_BEGINNING, $length=FactoryObject::LIMIT_ALL);
	
	protected function load($data_array) {
		// Set the item ID
		$this->setItemID(isset($data_array["itemId"])?$data_array["itemId"]:0);
	}
	
	
	# Data Methods
	public function delete() { return null; }
	
	public function save() {
		$this->refresh();
		return $this;
	}
	
	public function refresh() {
		$data_arrays = static::gatherData($this->getItemId());
		$this->load($data_arrays[0]);
		return $this;
	}
	
	
	# Getters
	public final function getItemId() { return $this->itemId; }
	
	public final function isUpdate() { return $this->getItemId() > 0; }
	
	
	# Setters
	protected final function setItemID($itemId) { $this->itemId = (int)$itemId; }
	
	
	# Static Methods
	public static function getObject($objectSelector) {
		// Takes in a single object ID and returns the associated object
		$data_arrays = static::gatherData((int)$objectSelector);
		
		if(sizeof($data_arrays) == 0)
			return new static();
		
		$newObject = new static();
		$newObject->load($data_arrays[0]);
		return $newObject;
	}
	
	public static function getObjects($objectSelectors, $start=FactoryObject::LIMIT_BEGINNING, $length=FactoryObject::LIMIT_ALL) {
		// Takes in either an array of object IDs or a clean query
		
		// If it's an array clean it and convert it to a string
		if(is_array($objectSelectors)) {
			foreach($objectSelectors as $key=>$objectID)
				$objectSelectors[$key] = DbConn::clean((int)$objectID);
			$objectSelectors = implode(",",$objectSelectors);
		}
		elseif(!is_string($objectSelectors))
			return array();
		
		// Load the data
		$data_arrays = static::gatherData($objectSelectors, $start, $length);
		if(sizeof($data_arrays) == 0)
			return array();
		
		// Create the objects
		$objectArray = array();
		foreach($data_arrays as $data_array) {
			$newObject = new static();
			$newObject->load($data_array);
			$objectArray[] = $newObject;
		}
		return $objectArray;
	}	
}
?>