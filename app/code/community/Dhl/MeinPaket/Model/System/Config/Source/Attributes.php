<?php
class Dhl_MeinPaket_Model_System_Config_Source_Attributes {
	protected $attributes;
	public function __construct() {
		$this->attributes = array (
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'None' ),
						'value' => 'None' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Default' ),
						'value' => 'Default' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Color' ),
						'value' => 'Farbe' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Size' ),
						'value' => 'Größe' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Dimension' ),
						'value' => 'Abmessung' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Quantity' ),
						'value' => 'Anzahl' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Width' ),
						'value' => 'Breite' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Length' ),
						'value' => 'Länge' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Format' ),
						'value' => 'Format' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Material' ),
						'value' => 'Material' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Flavor' ),
						'value' => 'Geschmacksrichtung' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Inscription' ),
						'value' => 'Beschriftung' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Model' ),
						'value' => 'Modell' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Platform' ),
						'value' => 'Plattform' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Radius' ),
						'value' => 'Radius' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Strength' ),
						'value' => 'Stärke' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Diameter' ),
						'value' => 'Durchmesser' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Cylinder' ),
						'value' => 'Zylinder' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Axis' ),
						'value' => 'Achse' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Bust Measurement' ),
						'value' => 'Brustumfang' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Cup' ),
						'value' => 'Cup' 
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Department' ),
						'value' => 'Department'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Sleeve\'s Length' ),
						'value' => 'Ärmellänge'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Frame' ),
						'value' => 'Fassung'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Main Material' ),
						'value' => 'Hauptmaterial'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Country of Manufacture' ),
						'value' => 'Herstellungsland'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Speed Index' ),
						'value' => 'Geschwindigkeitsindex'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Height' ),
						'value' => 'Höhe'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Year' ),
						'value' => 'Year'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Collection' ),
						'value' => 'Kollektion'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Collar' ),
						'value' => 'Kragenform'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Brand' ),
						'value' => 'Marke'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Subject' ),
						'value' => 'Motiv'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Optics' ),
						'value' => 'Optik'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Sets' ),
						'value' => 'Sets'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Language' ),
						'value' => 'Sprache'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Style' ),
						'value' => 'Stil'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Type' ),
						'value' => 'Typ'
				),
				array (
						'label' => Mage::helper ( 'meinpaket/data' )->__ ( 'Degree of Grinding' ),
						'value' => 'Mahlgrad'
				)
		);
	}
	public function toOptionArray($addEmpty = true) {
		return $this->attributes;
	}
	public function toSelectArray() {
		$result = array ();
		
		foreach ( $this->attributes as $attribute ) {
			$result [$attribute ['value']] = $attribute ['label'];
		}
		
		return $result;
	}
}
