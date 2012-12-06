<?php
###
# Info:
#  Last Updated 2011
#  Daniel Schultz
#
###
require_once("DbConn.php");
require_once("FactoryObject.php");
require_once("JSONObject.php");
require_once("Video.php");

class Clip extends FactoryObject implements JSONObject {
	
	# Constants
	
	# Static Variables
	
	
	# Instance Variables
	private $videoId; // int
	private $start; // long
	private $stop; // string
	
	
	# Caches
	private $claim;
	
	
	# FactoryObject Methods
	protected static function gatherData($objectString, $start=FactoryObject::LIMIT_BEGINNING, $length=FactoryObject::LIMIT_ALL) {
		$data_arrays = array();
		
		// Load an empty object
		if($objectString === FactoryObject::INIT_EMPTY) {
			$data_array = array();
			$data_array['itemId'] = 0;
			$data_array['videoId'] = 0;
			$data_array['start'] = "";
			$data_array['stop'] = "";
			$data_arrays[] = $data_array;
			return $data_arrays;
		}
		
		// Load a default object
		if($objectString === FactoryObject::INIT_DEFAULT) {
			$data_array = array();
			$data_array['itemId'] = 0;
			$data_array['videoId'] = 0;
			$data_array['start'] = "";
			$data_array['stop'] = "";
			$data_arrays[] = $data_array;
			return $data_arrays;
		}
		
		// Set up for lookup
		$mysqli = DbConn::connect();
		
		// Load the object data
		$query_string = "SELECT clips.id AS itemId,
							   clips.video_id AS videoId,
							   clips.start AS start,
							   clips.stop AS stop
						  FROM clips
						 WHERE clips.id IN (".$objectString.")";
		if($length != FactoryObject::LIMIT_ALL) {
			$query_string .= "
						 LIMIT ".DbConn::clean($start).",".DbConn::clean($length);
		}
		
		$result = $mysqli->query($query_string)
			or print($mysqli->error);
		
		while($resultArray = $result->fetch_assoc()) {
			$data_array = array();
			$data_array['itemId'] = $resultArray['itemId'];
			$data_array['videoId'] = $resultArray['videoId'];
			$data_array['start'] = $resultArray['start'];
			$data_array['stop'] = $resultArray['stop'];
			$data_arrays[] = $data_array;
		}
		
		$result->free();
		return $data_arrays;
	}
	
	public function load($data_array) {
		parent::load($data_array);
		$this->videoId = isset($data_array["videoId"])?$data_array["videoId"]:0;
		$this->start = isset($data_array["start"])?$data_array["start"]:0;
		$this->stop = isset($data_array["stop"])?$data_array["stop"]:0;
	}
	
	
	# JsonObject Methods
	public function toJson($contentStart=null, $contentLength=null) {
		$json = '{
			"id": '.DbConn::clean($this->getItemId()).',
			"video_id": '.DbConn::clean($this->getVideoId()).',
			"start": '.DbConn::clean($this->getStart()).',
			"stop": '.DbConn::clean($this->getStop()).'
		}';
		return $json;
	}
	
	
	# Data Methods
	public function validate() {
		return true;
	}
	
	public function save() {
		if(!$this->validate()) return;
		
		$mysqli = DbConn::connect();
		
		if($this->isUpdate()) {
			// Update an existing record
			$query_string = "UPDATE clips
							   SET clips.video_id = ".DbConn::clean($this->getVideoId()).",
							   AND clips.start = ".DbConn::clean($this->getStart()).",
							   AND clips.stop = ".DbConn::clean($this->getStop())."
							 WHERE snippets.id = ".DbConn::clean($this->getItemId());
							
			$mysqli->query($query_string) or print($mysqli->error);
		} else {
			// Create a new record
			$query_string = "INSERT INTO clips
								   (clips.id,
									clips.video_id,
									clips.start,
									clips.stop)
							VALUES (0,
									".DbConn::clean($this->getVideoId()).",
									".DbConn::clean($this->getStart()).",
									".DbConn::clean($this->getStop()).")";
			
			$mysqli->query($query_string) or print($mysqli->error);
			$this->setItemID($mysqli->insert_id);
		}
		
		// Parent Operations
		return parent::save();
	}
	
	public function delete() {
		parent::delete();
		$mysqli = DbConn::connect();
		
		// Delete this record
		$query_string = "DELETE FROM clips
							  WHERE clips.id = ".DbConn::clean($this->getItemId());
		$mysqli->query($query_string);
		
		// Delete tokens associated with this record
		$tokens = Token::getObjectsBySnippet($this->getItemId());
		foreach($tokens as $token)
			$token->delete();
		
	}
	
	
	# Getters
	public function getVideoId() { return $this->videoId; }
	
	public function getStart() { return $this->start; }
	
	public function getEnd() { return $this->end; }
	
	
	# Setters
	public function setVideoId($int) { $this->videoId = $int; }
	
	public function setStart($lng) { $this->start = $lng; }
	
	public function setContent($end) {$this->end = $lng; }
	
	
	# Static Methods
	public static function getObjectsByVideoId($videoId) {
		$query_string = "SELECT distinct clips.id
						  FROM clips
						 WHERE clips.video_id = ".DbConn::clean($videoId);
		return Clip::getObjects($query_string);
	}
	
}

?>