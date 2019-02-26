<?php
require_once("SimpleRest.php");
require_once("Main.php");
require_once("Recycler.php");
require_once("DBHandler.php");
		
class NagiosRestHandler extends SimpleRest {

function detectRequestBody() {
	$postData = file_get_contents('php://input');
	$xml = simplexml_load_string($postData);
	return $xml;
}

        function show_all()
        {
                var_dump("initiating showall");
                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, 200);

                $objDB=new DBHandler();
                $response=$objDB->showAllIPs();
                var_dump($response);
                $xml = new SimpleXMLElement('<?xml version="1.0"?><result>' . $response . '</result>');
                echo $xml->asXML();
        }

        function delete_ip($ip)
        {
                var_dump("initiating delete");
                $requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, 200);

                $objDB=new DBHandler();
                $response=$objDB->deleteIps($ip);
                var_dump($response);
                $xml = new SimpleXMLElement('<?xml version="1.0"?><result>' . $response . '</result>');
                echo $xml->asXML();
        }

	function recycle()
	{
		var_dump("initiating recycling");
		$requestContentType = $_SERVER['HTTP_ACCEPT'];
                $this ->setHttpHeaders($requestContentType, 200);	
		
		$objRecycle=new Recycler();
		$response=$objRecycle->initiate_recycle();
		var_dump($response);
		$xml = new SimpleXMLElement('<?xml version="1.0"?><result>' . $response . '</result>');
                echo $xml->asXML();
	}
	

	function registerInstance() {	

		$inputXML = $this->detectRequestBody();

		if(empty($inputXML)) {
			$statusCode = 404;
			$inputXML = array('error' => 'No input found!');		
		} else {
			$statusCode = 200;
		}

		$requestContentType = $_SERVER['HTTP_ACCEPT'];
		$this ->setHttpHeaders($requestContentType, $statusCode);
				
		if(strpos($requestContentType,'application/json') !== false){
			$response = $this->encodeJson($inputXML);
			echo $response;
		} else if(strpos($requestContentType,'text/html') !== false){
			$response = $this->encodeHtml($inputXML);
			echo $response;
		} else if(strpos($requestContentType,'application/xml') !== false){
			$response = $this->encodeXml($inputXML);
			echo $response;
		}
	}
	
	public function encodeHtml($inputXML) {
	
		$htmlResponse = "<table border='1'>";
		foreach($inputXML as $key=>$value) {
    			$htmlResponse .= "<tr><td>". $key. "</td><td>". $value. "</td></tr>";
		}
		$htmlResponse .= "</table>";
		return $htmlResponse;		
	}
	
	public function encodeJson($inputXML) {
		$jsonResponse = json_encode($inputXML);
		return $jsonResponse;		
	}
	
	public function encodeXml($inputXML) {
		// creating object of SimpleXMLElement
		
		$mainObj = new Main;
		$response= $mainObj->process($inputXML);

		//$ip = $inputXML->ip;
		$xml = new SimpleXMLElement('<?xml version="1.0"?><result>' . $response . '</result>');
		return $xml->asXML();
	}
}
?>
