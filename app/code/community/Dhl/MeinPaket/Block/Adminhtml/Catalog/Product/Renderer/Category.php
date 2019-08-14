<?php
/**
 * Product allyouneed category attribute frontend.
 *
 * @category   Dhl
 * @package	Dhl_Allyouneed
 */
class Dhl_MeinPaket_Block_Adminhtml_Catalog_Product_Renderer_Category extends Varien_Data_Form_Element_Abstract {
	public function __construct($attributes = array()) {
		parent::__construct ( $attributes );
		$this->setType ( 'text' );
	}
	
	/**
	 *
	 * {@inheritDoc}
	 *
	 * @see Varien_Data_Form_Element_Abstract::getAfterElementHtml()
	 */
	public function getAfterElementHtml() {
		$html = parent::getAfterElementHtml ();
		$html .= $this->getTree ();
		$html .= $this->getScript ();
		return $html;
	}
	
	/**
	 */
	protected function getTreeJson() {
		/* @var $source Dhl_MeinPaket_Model_Entity_Attribute_Source_MeinPaketCategory */
		$source = Mage::getModel ( "meinpaket/entity_attribute_source_meinPaketCategory" );
		return Mage::helper ( 'core' )->jsonEncode ( $source->getAllOptions () );
	}
	/**
	 */
	protected function getTree() {
		return "<div class=\"tree x-tree\" id=\"" . $this->getId () . "_tree\"></div>";
	}
	
	/**
	 * Get <script> tag
	 *
	 * @return string
	 */
	protected function getScript() {
		$html = '<script type="text/javascript">' . "\n";
		$html .= "
(function() {
	if (typeof Ext !== 'undefined' && Ext && Ext.EventManager && Ext.EventManager.onDocumentReady) {
		var treeData = " . $this->getTreeJson () . ";
		var nodeId = '" . $this->getId () . "';
		var inputNode = $(nodeId);
		
		var labelNode = 			labelNode = document.createElement('span');
		labelNode.id = '" . $this->getId () . "_label';
		labelNode.for = '" . $this->getId () . "';
		labelNode.style.marginLeft = '5px';
				
		Ext.EventManager.onDocumentReady(function() {
			// Create label
			inputNode.parentNode.insertBefore(labelNode, inputNode.nextSibling);
			
			var selectionModel = new Ext.tree.DefaultSelectionModel()

			var tree = new Ext.tree.TreePanel(nodeId + '_tree', {
				animate : false,
				loader : false,
				enableDD : false,
				containerScroll : true,
				selModel : selectionModel,
				rootVisible : false,
				singleExpand : false,
			});
				
			// set the root node
			var root = new Ext.tree.TreeNode({
				text : 'root',
				draggable : false,
				checked : false,
				id : '__root__',
			});

			tree.setRootNode(root);

			buildCategoryTree(root, treeData);

			selectionModel.addListener('beforeselect', beforeSelectionChange);
			selectionModel.addListener('selectionchange', selectionChange);

			var initialNodeId = inputNode.value;
			var initialNode = tree.getNodeById(initialNodeId);

			// render the tree
			tree.render();

			if (initialNode) {
				tree.expandPath(initialNode.getPath());
				selectionModel.select(initialNode);
			}
		})

		function beforeSelectionChange(model, _new, _old) {
			return _new.isLeaf();
		}

		function selectionChange(model, node) {
			inputNode.value = node.id;
			labelNode.innerHTML = node.text;
		}

		function buildCategoryTree(parent, config) {
			if (!config) {
				return null;
			}

			if (parent && Array.isArray(config) && config.length) {
				for (var i = 0; i < config.length; i++) {
					var hasChildren = Array.isArray(config[i].value);
					config[i].uiProvider = Ext.tree.TreeNodeUI;
					config[i].id = config[i].code;
					config[i].text = config[i].label;
					config[i].expanded = false;
					config[i].leaf = !hasChildren;
					var node = new Ext.tree.TreeNode(config[i]);
					parent.appendChild(node);
					if (Array.isArray(config[i].value)) {
						buildCategoryTree(node, config[i].value);
					}
				}
			}
		}
	}
})();
		";
		
		$html .= '</script>' . "\n";
		return $html;
	}
}
