<?php

if (!defined('ABSPATH')) {
	exit;
}

require_once('class.document.php');

class HTMLToPlaintextConverter
{
	public function __construct()
	{
		
	}
	
	private function convertInput($input)
	{
		switch(strtolower($input->getAttribute('type')))
		{
			case 'checkbox':
			case 'radio':
				$replacement = $input->ownerDocument->createElement('span');
				$replacement->appendText( $input->hasAttribute('checked') ? 'Yes' : 'No' );
				$input->parentNode->replaceChild($replacement, $input);
				break;
				
			case 'submit':
				$input->remove();
				break;
			
			default:
				$replacement = $input->ownerDocument->createElement('span');
				if(strtolower($input->getAttribute('type')) == 'password')
					$replacement->appendText('********');
				else
					$replacement->appendText($input->getAttribute('value'));
				$replacement->setInlineStyle('text-decoration', 'underline');
				$input->parentNode->replaceChild($replacement, $input);
				break;
		}
	}
	
	private function convertSelect($select)
	{
		$selected = $select->querySelector('option[selected]');
		$replacement = $select->ownerDocument->createElement('span');
		$replacement->appendText($selected->textContent);
		$replacement->setInlineStyle('text-decoration', 'underline');
		$select->parentNode->replaceChild($replacement, $select);
	}
	
	private function convertTextarea($textarea)
	{
		$replacement = $textarea->ownerDocument->createElement('span');
		$replacement->appendText($textarea->textContent);
		$textarea->parentNode->replaceChild($replacement, $textarea);
	}
	
	public function convert($html, $populate=null)
	{
		$document = new WPGMZA\DOMDocument();
		$document->loadHTML($html);
		
		if($populate)
			$document->populate($populate);
		
		foreach($document->querySelectorAll('input, select, textarea') as $element)
		{
			switch(strtolower($element->tagName))
			{
				case 'input':
					$this->convertInput($element);
					break;
					
				case 'select':
					$this->convertSelect($element);
					break;
					
				case 'textarea':
					$this->convertTextarea($element);
					break;
			}
		}
		
		return $document->textContent;
	}
}

?>