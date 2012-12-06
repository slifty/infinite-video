<?php
###
# Info:
#  Indicates that an object will have a method that returns its JSON representation
#  
###

interface JsonObject {
	public function toJson();
}
?>