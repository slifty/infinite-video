<?php
###
# Info:
#  Indicates that an object will have a method that returns its Json representation
#  
###

interface JsonObject {
	public function toJson();
}
?>