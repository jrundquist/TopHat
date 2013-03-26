<?php

function factory_request($url = false){
	if(!$url)
		return false;
	return Router::route($url);
}