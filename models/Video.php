<?php
###
# Info:
#  Last Updated 2011
#  Daniel Schultz
#
###

require_once("DbConn.php");
require_once("FactoryObject.php");
require_once("JsonObject.php");
require_once("Clip.php");

class Video extends FactoryObject implements JsonObject {
	
	# Constants
	
	
	# Static Variables
	
	
	# Instance Variables
	private $url; // string
	private $dateCreated; //timestamp
	
	
	# Caches
	private $clips;
	
	
	# FactoryObject Methods
	protected static function gatherData($objectString, $start=FactoryObject::LIMIT_BEGINNING, $length=FactoryObject::LIMIT_ALL) {
		$data_arrays = array();
		
		// Load an empty object
		if($objectString === FactoryObject::INIT_EMPTY) {
			$data_array = array();
			$data_array['itemId'] = 0;
			$data_array['url'] = "";
			$data_array['dateCreated'] = 0;
			$data_arrays[] = $data_array;
			return $data_arrays;
		}
		
		// Load a default object
		if($objectString === FactoryObject::INIT_DEFAULT) {
			$data_array = array();
			$data_array['itemId'] = 0;
			$data_array['url'] = "";
			$data_array['dateCreated'] = 0;
			$data_arrays[] = $data_array;
			return $data_arrays;
		}
		
		// Set up for lookup
		$mysqli = DbConn::connect();
		
		// Load the object data
		$query_string = "SELECT videos.id AS itemId,
							   videos.url AS url,
							   unix_timestamp(videos.date_created) as dateCreated
						  FROM videos
						 WHERE videos.id IN (".$objectString.")";
		if($length != FactoryObject::LIMIT_ALL) {
			$query_string .= "
						 LIMIT ".DbConn::clean($start).",".DbConn::clean($length);
		}
		
		$result = $mysqli->query($query_string)
			or print($mysqli->error);
		
		while($resultArray = $result->fetch_assoc()) {
			$data_array = array();
			$data_array['itemId'] = $resultArray['itemId'];
			$data_array['url'] = $resultArray['url'];
			$data_array['dateCreated'] = $resultArray['dateCreated'];
			$data_arrays[] = $data_array;
		}
		
		$result->free();
		return $data_arrays;
	}
	
	public function load($data_array) {
		parent::load($data_array);
		$this->url = isset($data_array["url"])?$data_array["url"]:"";
		$this->dateCreated = isset($data_array["dateCreated"])?$data_array["dateCreated"]:0;
	}
	
	
	# JsonObject Methods
	public function toJson($contentStart=null, $contentLength=null) {
		$clips = $this->getClips();
		$clipsJsonArray = array();
		foreach($clips as $clip)
			$clipsJsonArray[] = $clip->toJson();
		$clipsJson = "[".implode(",",$clipsJsonArray)."]";
		
		$json = '{';
		$json .= '"id": '.DbConn::clean($this->getItemId()).',';
		$json .= '"url": '.DbConn::clean($this->getUrl()).',';
		$json .= '"clips": '.$clipsJson;
		$json .= '}';
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
			$query_string = "UPDATE videos
							   SET videos.url = ".DbConn::clean($this->getUrl())."
							 WHERE videos.id = ".DbConn::clean($this->getItemId());
							
			$mysqli->query($query_string) or print($mysqli->error);
		} else {
			// Create a new record
			$query_string = "INSERT INTO videos
								   (videos.id,
									videos.url,
									videos.date_created)
							VALUES (0,
									".DbConn::clean($this->getUrl()).",
									NOW())";
			
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
		$query_string = "DELETE FROM videos
							  WHERE videos.id = ".DbConn::clean($this->getItemId());
		$mysqli->query($query_string);
	}
	
	
	# Getters
	public function getUrl() { return $this->url; }
	
	public function getDateCreated() { return $this->dateCreated; }
	
	public function getClips() {
		if($this->clips != null)
			return $this->clips;
		
		$query_string = "SELECT clips.id
						  FROM clips
						 WHERE clips.video_id = ".DbConn::clean($this->getItemId());
		
		return $this->clips = Clip::getObjects($query_string);
	}
	
	
	# Setters
	public function setUrl($str) { $this->url = $str; }
	
	
	# Static Methods
	public static function getAllObjects() {
		$query_string = "SELECT videos.id as itemId 
						   FROM videos";
		return Video::getObjects($query_string);
	}
	
	public static function getObjectsWithClips() {
		$query_string = "SELECT videos.id as itemId 
						   FROM videos
						  WHERE EXISTS (select * from clips where clips.video_id = videos.id)";
		return Video::getObjects($query_string);
	}
}

?>