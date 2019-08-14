<?php
class Dhl_MeinPaketCommon_Model_Xml_Response_DataResponse extends Dhl_MeinPaketCommon_Model_Xml_Response_Abstract {
	private $confirmations = array ();
	private $bestPrices = array ();
	private $productData = array ();
	private $merchantData = array ();
	private $quotaData = array ();
	
	/**
	 * Default constructor.
	 *
	 * @param DOMElement $domElement        	
	 */
	public function __construct(DOMElement $domElement) {
		parent::__construct ( $domElement );
		assert ( $domElement->localName == 'dataResponse' );
		
		foreach ( $domElement->childNodes as $downloadResponseEntries ) {
			switch ($downloadResponseEntries->localName) {
				case 'confirmation' :
					$this->confirmations [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_Confirmation ( $downloadResponseEntries );
					break;
				case 'bestPrice' :
					$this->bestPrices [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_BestPrice ( $downloadResponseEntries );
					break;
				case 'productData' :
					$this->productData [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_ProductData ( $downloadResponseEntries );
					break;
				case 'merchantData' :
					$this->merchantData [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_MerchantData ( $downloadResponseEntries );
					break;
				case 'quotaData' :
					$this->quotaData [] = new Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_QuotaData ( $downloadResponseEntries );
					break;
			}
		}
	}
	
	/**
	 *
	 * @return array
	 */
	public function getConfirmations() {
		return $this->confirmations;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getBestPrices() {
		return $this->bestPrices;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getProductData() {
		return $this->productData;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getMerchantData() {
		return $this->merchantData;
	}
	
	/**
	 *
	 * @return array
	 */
	public function getQuotaData() {
		return $this->quotaData;
	}
	public function process() {
		$transaction = Mage::getSingleton ( 'core/resource' )->getConnection ( 'core_write' );
		
		try {
			$transaction->beginTransaction ();
			/* @var $bestPrice Dhl_MeinPaketCommon_Model_Xml_Response_Partial_DataResponse_BestPrice */
			
			$collection = Mage::getModel ( 'meinpaket/bestPrice' )->getCollection ();
			foreach ( $collection->getItems () as $price ) {
				$price->delete ();
			}
			
			foreach ( $this->bestPrices as $bestPrice ) {
				if (count ( $bestPrice->getCommonErrors () )) {
					continue;
				}
				
				/* @var $bestPriceModel Dhl_MeinPaketCommon_Model_BestPrice */
				$bestPriceModel = Mage::getModel ( 'meinpaket/bestPrice' );
				$bestPriceModel->setProductId ( $bestPrice->getProductId () );
				$bestPriceModel->setPrice ( $bestPrice->getPrice () );
				$bestPriceModel->setPriceCurrency ( $bestPrice->getPriceCurrency () );
				$bestPriceModel->setDeliveryCost ( $bestPrice->getDeliveryCost () );
				$bestPriceModel->setDeliveryCostCurrency ( $bestPrice->getDeliveryCostCurrency () );
				$bestPriceModel->setDeliveryTime ( $bestPrice->getDeliveryTime () );
				$bestPriceModel->setActiveOffers ( $bestPrice->getActiveOffers () );
				$bestPriceModel->setOwnership ( $bestPrice->getOwnership () );
				$bestPriceModel->setOwningDealerCode ( $bestPrice->getOwningDealerCode () );
				$bestPriceModel->setCreatedAt ( Varien_Date::now () );
				$bestPriceModel->save ();
			}
			$transaction->commit ();
		} catch ( Exception $e ) {
			$transaction->rollback ();
			Mage::logException ( $e );
			throw $e;
		}
	}
}
