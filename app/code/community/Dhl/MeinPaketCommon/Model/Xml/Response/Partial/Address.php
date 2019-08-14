<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_Partial_Address extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	/**
	 * Salutation.
	 *
	 * @var string
	 */
	private $salutation;
	/**
	 * Company.
	 *
	 * @var string
	 */
	private $company;
	/**
	 * First name.
	 *
	 * @var string
	 */
	private $firstName;
	/**
	 * Last name.
	 *
	 * @var string
	 */
	private $lastName;
	/**
	 * Street.
	 *
	 * @var string
	 */
	private $street;
	/**
	 * Used for house number or packstationId.
	 *
	 * @var string
	 */
	private $houseNumber;
	/**
	 * Used for addressAddition.
	 *
	 * @var string
	 */
	private $addressAddition;
	/**
	 * Used for customerId.
	 *
	 * @var string
	 */
	private $customerId;
	/**
	 * Zip code.
	 *
	 * @var string
	 */
	private $zipCode;
	/**
	 * City.
	 *
	 * @var string
	 */
	private $city;
	/**
	 * Country.
	 *
	 * @var string
	 */
	private $country;
	
	/**
	 * Default constructor.
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'deliveryAddress' || $domElement->localName == 'billingAddress' );
		
		foreach ( $domElement->childNodes as $childNode ) {
			switch ($childNode->localName) {
				case 'salutation' :
					$this->salutation = $childNode->nodeValue;
					break;
				case 'company' :
					$this->company = $childNode->nodeValue;
					break;
				case 'firstName' :
					$this->firstName = $childNode->nodeValue;
					break;
				case 'lastName' :
					$this->lastName = $childNode->nodeValue;
					break;
				case 'street' :
					$this->street = $childNode->nodeValue;
					break;
				case 'houseNumber' :
					$this->houseNumber = $childNode->nodeValue;
					break;
				case 'addressAddition' :
					$this->addressAddition = $childNode->nodeValue;
					break;
				case 'zipCode' :
					$this->zipCode = $childNode->nodeValue;
					break;
				case 'city' :
					$this->city = $childNode->nodeValue;
					break;
				case 'country' :
					$this->country = $childNode->nodeValue;
					break;
				case 'packstationId' :
					$this->street = 'Packstation';
					$this->houseNumber = $childNode->nodeValue;
					break;
				case 'customerId' :
					$this->customerId = $childNode->nodeValue;
					break;
			}
		}
	}
	public function getSalutation() {
		return $this->salutation;
	}
	public function getCompany() {
		return $this->company;
	}
	public function getFirstName() {
		return $this->firstName;
	}
	public function getLastName() {
		return $this->lastName;
	}
	public function getStreet() {
		return $this->street;
	}
	public function getHouseNumber() {
		return $this->houseNumber;
	}
	public function getAddressAddition() {
		return $this->addressAddition;
	}
	public function getZipCode() {
		return $this->zipCode;
	}
	public function getCity() {
		return $this->city;
	}
	public function getCountry() {
		return $this->country;
	}
	public function getCustomerId() {
		return $this->customerId;
	}
}