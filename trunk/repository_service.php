<?php

class RepositoryService
{
    public function __construct()
    {}

    // ********** XML related functions *******************
/*    public function getOperationResult($operationResult)
    {
    	$domDocument = new DOMDocument();
     	$domDocument->loadXML($operationResult);
 	
     	$operationResultValues = array();
 	
     	foreach( $domDocument->childNodes AS $ChildNode )
       	{
           		if ( $ChildNode->nodeName != '#text' )
           		{
           		
               		if ($ChildNode->nodeName == "operationResult")
               		{
               			foreach( $ChildNode->childNodes AS $ChildChildNode )
       				{
   					
       					if ( $ChildChildNode->nodeName == 'returnCode' )
           					{	
           						$operationResultValues['returnCode'] = $ChildChildNode->nodeValue;
               				}
               				else if ( $ChildChildNode->nodeName == 'returnMessage' )
           					{	
           						$operationResultValues['returnMessage'] = $ChildChildNode->nodeValue;
               				}
               			}
               		}
               	}
            }
        
            return $operationResultValues;
    }
*/

    public function getResourceDescriptors($operationResult)
    {
    	$domDocument = new DOMDocument();
     	$domDocument->loadXML($operationResult);
 	
     	$folders = array();
     	$count = 0;
 	
     	foreach( $domDocument->childNodes AS $ChildNode )
       	{
           		if ( $ChildNode->nodeName != '#text' )
           		{
           		
               		if ($ChildNode->nodeName == "operationResult")
               		{
               			foreach( $ChildNode->childNodes AS $ChildChildNode )
       				{
   					
       					if ( $ChildChildNode->nodeName == 'resourceDescriptor' )
           					{	
           						$resourceDescriptor = $this->readResourceDescriptor($ChildChildNode);
       						$folders[ $count ] = $resourceDescriptor;
               					$count++;
               				}
               			}
               		}
           		
           		}
       	}
   	
       	return $folders;
    }

    public function readResourceDescriptor($node)
    {
    	$resourceDescriptor = array();
	
    	$resourceDescriptor['name'] = $node->getAttributeNode("name")->value;
            $resourceDescriptor['uri'] =  $node->getAttributeNode("uriString")->value;
            $resourceDescriptor['type'] = $node->getAttributeNode("wsType")->value;
	
    	$resourceProperties = array();
    	$subResources = array();
    	$parameters = array();
	
    	// Read subelements...
    	foreach( $node->childNodes AS $ChildNode )
       	{
       		if ( $ChildNode->nodeName == 'label' )
    		{
    			$resourceDescriptor['label'] = 	$ChildNode->nodeValue;
    		}
    		else if ( $ChildNode->nodeName == 'description' )
    		{
    			$resourceDescriptor['description'] = 	$ChildNode->nodeValue;
    		}
    		else if ( $ChildNode->nodeName == 'resourceProperty' )
    		{
    			//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
    			// read properties...
    			$resourceProperty = $this->addReadResourceProperty($ChildNode );
    			$resourceProperties[ $resourceProperty["name"] ] = $resourceProperty;
    		}
    		else if ( $ChildNode->nodeName == 'resourceDescriptor' )
    		{
    			array_push( $subResources, $this->readResourceDescriptor($ChildNode));
    		}
    		else if ( $ChildNode->nodeName == 'parameter' )
    		{
    			$parameters[ $ChildNode->getAttributeNode("name")->value ] =  $ChildNode->nodeValue;
    		}
    	}
	
    	$resourceDescriptor['properties'] = $resourceProperties;
    	$resourceDescriptor['resources'] = $subResources;
    	$resourceDescriptor['parameters'] = $parameters;
	
	
    	return $resourceDescriptor;
    }

    public function addReadResourceProperty($node)
    {
    	$resourceProperty = array();
	
    	$resourceProperty['name'] = $node->getAttributeNode("name")->value;
        
    	$resourceProperties = array();
	
    	// Read subelements...
    	foreach( $node->childNodes AS $ChildNode )
       	{
       		if ( $ChildNode->nodeName == 'value' )
    		{
    			$resourceProperty['value'] = $ChildNode->nodeValue;
    		}
    		else if ( $ChildNode->nodeName == 'resourceProperty' )
    		{
    			//$resourceDescriptor['resourceProperty'] = $ChildChildNode->nodeValue;
    			// read properties...
    			array_push( $resourceProperties, $this->addReadResourceProperty($ChildNode ) );
    		}
    	}
	
    	$resourceProperty['properties'] = $resourceProperties;
	
    	return $resourceProperty;
    }
}

?>