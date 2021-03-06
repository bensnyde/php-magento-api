<?php

/**
 * PHP Library for Magento SOAP API
 *
 *  http://www.magentocommerce.com/api/soap/introduction.html
 *
 * @author     Benton Snyder
 * @website    http://www.bensnyde.me
 * @created    5/19/2013
 * @updated    4/2/2015
 */

class Magento
{
    private $_api_instance; //Magento API instance
    private $_api_session_key; //Magento login session key

    /**
     * Constructor
     *
     * @access      public
     * @param       string wsdl url, string username, string password
     * @return
    */
    function __construct($wsdl_url, $username, $password) {
        if(!extension_loaded('soap'))
            die('Missing SOAP extension');

        $this->initialize($wsdl_url, $username, $password);
    }

    /**
     * SOAP authentication request
     *
     * @access      private
     * @param
     * @return
    */
    private function initialize($wsdl_url, $username, $password) {
        $this->_api_instance = new SoapClient($wsdl_url);

        try {
            $this->_api_session_key = $this->_api_instance->login($username, $password);
        }
        catch(SoapFault $soap_ex) {
            die($soap_ex->getMessage());
        }
        catch(Exception $ex) {
            die($ex->getMessage());
        }
    }

            /**
     * Calls SOAP request
     *
     * @access     private
     * @param      string api path, array arguments
     * @return     soap response
    */
    private function mQuery($command, $args = array()) {
        try {
            return $this->_api_instance->call($this->_api_session_key, $command, $args);
        }
        catch(SoapFault $soap_ex) {
            die($soap_ex->getMessage());
        }
        catch(Exception $ex) {
            die($ex->getMessage());
        }
    }


    # ***** catalog_category *****

    /**
     * Allows you to set/get the current store view
     *
     * @access      public
     * @param       string storeViewID
     * @return      int storeViewID
    */
    public function catalogCategory_currentStore($storeViewID) {
        return $this->mQuery("catalog_category.currentStore", array("storeView" => $storeViewID));
    }

    /**
     * Allows you to retrieve the hierarchical tree of categories
     *
     * @access      public
     * @param       *string parentID, *string storeViewID
     * @return      array catalogCategoryTree
    */
    public function catalogCategory_tree($parentID = null, $storeViewID = null) {
        $data = array();
        $data['parentId'] = $parentID;
        $data['storeView'] = $storeViewID;

        return $this->mQuery("catalog_category.tree", $data);
    }

    /**
     * Allows you to retrieve one level of categories by a website, a store view, or a parent category
     *
     * @access      public
     * @param       *string websiteID, *string storeViewID, *string parentCategory
     * @return      array CatalogCategoryEntitiesNoChildren
    */
    public function catalogCategory_level($websiteID = null, $storeViewID = null, $parentCategoryID = null) {
        $data = array();
        $data['website'] = $websiteID;
        $data['storeView'] = $storeViewID;
        $data['parentCategory'] = $parentCategoryID;

        return $this->mQuery("catalog_category.level", $data);
    }

    /**
     * Allows you to retrieve information about the required category
     *
     * @access      public
     * @param       int categoryID, *string storeViewID, *ArrayOfString attributes
     * @return      array catalogCategoryInfo
    */
    public function catalogCategory_info($categoryID, $storeViewID = null, Array $attributes = array()) {
        $data = array();
        $data['categoryId'] = (int)$categoryID;
        $data['attributes'] = $attributes;
        $data['storeView'] = $storeViewID;

        return $this->mQuery("catalog_category.info", $data);
    }

    /**
     * Create a new category and return its ID
     *
     * @access      public
     * @param       int parent category id, array catalogCategoryEntityCreate, *string store view id
     * @return      int attribute id
    */
    public function catalogCategory_create($parentID, Array $categoryData, $storeID = null) {
        $data = array();
        $data['parentId'] = $parentID;
        $data['categoryData'] = $categoryData;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_category.create", $data);
    }

    /**
     * Update the required category
     *
     * @access      public
     * @param       int category id, array catalogCategoryEntityCreate, *string store view id
     * @return      boolean result
    */
    public function catalogCategory_update($categoryID, Array $categoryData, $storeID = null) {
        $data = array();
        $data['categoryId'] = $categoryID;
        $data['categoryData'] = $categoryData;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_category.update", $data);
    }

    /**
     * Allows you to move the required category in the category tree
     *
     * @access      public
     * @param       int category id to be moved, int id of new parent category, *id of category after which required category will be moved
     * @return
    */
    public function catalogCategory_move($categoryID, $parentID, $afterID = null) {
        $data = array();
        $data['categoryId'] = $categoryID;
        $data['parentId'] = $parentID;
        $data['afterId'] = $afterID;

        return $this->mQuery("catalog_category.move", $data);
    }

    /**
     * Allows you to delete the required category
     *
     * @access      public
     * @param       int category id
     * @return      boolean result
    */
    public function catalogCategory_delete($categoryID) {
        return $this->mQuery("catalog_category.delete", array("categoryId" => $categoryID));
    }

    /**
     * Retrieve the list of products assigned to a required category
     *
     * @access      public
     * @param       int category id
     * @return      array catalogAssignedProduct
    */
    public function catalogCategory_assignedProducts($categoryID) {
        return $this->mQuery("catalog_category.assignedProducts", array("categoryId" => $categoryID));
    }

    /**
     * Assign a product to the required category
     *
     * @access      public
     * @param       int category id, string product ID/SKU, *string position of product in category, string whether productID/SKU is passed in 'product' argument
     * @return      boolean result
    */
    public function catalogCategory_assignProduct($categoryID, $productID, $position = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['categoryId'] = $categoryID;
        $data['productId'] = $productID;
        $data['position'] = $position;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_category.assignProduct", $data);
    }

    /**
     * Allows you to update the product assigned to a category
     *
     * @access      public
     * @param       int category id, string product ID/SKU, *string position of product in category, string whether productID/SKU is passed in 'product' argument
     * @return      boolean result
    */
    public function catalogCategory_updateProduct($categoryID, $productID, $position = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['categoryId'] = $categoryID;
        $data['productId'] = $productID;
        $data['position'] = $position;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_category.updateProduct", $data);
    }

    /**
     * Allows you to remove the product assignment from the category
     *
     * @access      public
     * @param       int category id, string product ID/SKU, *string whether productID/SKU is passed in 'product' argument
     * @return      boolean result
    */
    public function catalogCategory_removeProduct($categoryID, $productID, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['categoryId'] = $categoryID;
        $data['productId'] = $productID;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_category.removeProduct", $data);
    }


    # ***** catalog_category_attribute *****

    /**
     * Allows you to set/get the current store view
     *
     * @access      public
     * @param       *string store view id
     * @return      int store view id
    */
    public function catalogCategoryAttribute_currentStore($storeID = null) {
        return $this->mQuery("catalog_product_attribute.currentStore", array('storeView'=>$storeID));
    }

    /**
     * Allows you to retrieve the list of category attributes
     *
     * @access      public
     * @param
     * @return      array catalogAttributeEntity
    */
    public function catalogCategoryAttribute_list() {
        return $this->mQuery("catalog_product_attribute.list");
    }

    /**
     * Allows you to retrieve the attribute options
     *
     * @access   public
     * @param    string attribute id, string store view id
     * @return   array catalogAttributeOptionEntity
    */
    public function catalogCategoryAttribute_options($attributeID, $storeID) {
        $data = array();
        $data['attributeId'] = $attributeID;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_category_attribute.options", $data);
    }


    # ***** catalog_product *****

    /**
     * Allows you to set/get the current store view
     *
     * @access      public
     * @param       *string store view id
     * @return      string store view id
    */
    public function catalogProduct_currentStore($storeID = null) {
        return $this->mQuery("catalog_product.currentStore", array('storeView' => $storeID));
    }

    /**
     * Allows you to retrieve the list of products
     *
     * @access      public
     * @param       *array filters by attributes, *string store view id
     * @return      array catalogProductEntity
    */
    public function catalogProduct_list($filters = array(), $storeID = null) {
        $data = array();
        $data['filters'] = $filters;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product.list", $data);
    }

    /**
     * Allows you to retrieve information about the required product
     *
     * @access      public
     * @param       string product id, *string store view id, *array catalogProductRequestAttributes, *string product identifier type
     * @return      array catalogProductReturnEntity
    */
    public function catalogProduct_info($productID, $storeID = null, Array $attributes = array(), $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['product'] = $productID;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['attributes'] = $attributes;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product.info", $data);
    }

    /**
     * Allows you to create a new product and return ID of the created product
     *
     * @access      public
     * @param       string product type, string set id, string product sku, array catalogProductCreateEntity, string store view id
     * @return      int id of created product
    */
    public function catalogProduct_create($type, $setID, $sku, Array $productData, $storeID) {
        $data = array();
        $data['type'] = $type;
        $data['set'] = $setID;
        $data['sku'] = $sku;
        $data['productData'] = $productData;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product.create", $data);
    }

    /**
     * Allows you to update the required product. Note that you should specify only those parameters which you want to be updated
     *
     * @access      public
     * @param       string product id, array catalogProductCreateEntity, *string store view id, *string product identifier type
     * @return      boolean result
    */
    public function catalogProduct_update($productID, Array $productData, $storeID = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['productData'] = $productData;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product.update", $data);
    }

    /**
     * Allows you to set the product special price
     *
     * @access      public
     * @param       string product id, string product special price, sring start date, string end date, *string store view id, *string product identifier type
     * @return      boolean result
    */
    public function catalogProduct_setSpecialPrice($productID, $specialPrice, $fromDate, $toDate, $storeID = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['product'] = $productID;
        $data['specialPrice'] = $specialPrice;
        $data['fromDate'] = $fromDate;
        $data['toDate'] = $toDate;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product.setSpecialPrice", $data);
    }

    /**
     * Allows you to get the product special price data
     *
     * @access      public
     * @param       string product id, string store view id, string product identifier type
     * @return
    */
    public function catalogProduct_getSpecialPrice($productID, $storeID, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['storeView'] = $storeID;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product.getSpecialPrice", $data);
    }

    /**
     * Allows you to delete the required product
     *
     * @access      public
     * @param       string product id, *string product identifier type
     * @return      boolean result
    */
    public function catalogProduct_delete($productId, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productId;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product.delete", $data);
    }

    /**
     * Get the list of additional attributes. Additional attributes are attributes that are not in the default set of attributes
     *
     * @access      public
     * @param       string product type, string attribute set id
     * @return      array
    */
    public function catalogProduct_listOfAdditionalAttributes($productType, $attributeSetID) {
        $data = array();
        $data['productType'] = $productType;
        $data['attributeSetId'] = $attributeSetID;

        return $this->mQuery("catalog_product.listOfAdditionalAttributes", $data);
    }


    # ***** product_attribute *****

    /**
     * Allows you to set/get the current store view
     *
     * @access      public
     * @param       *string store view id
     * @return      int store view id
    */
    public function productAttribute_currentStore($storeID = null) {
        return $this->mQuery("catalog_product_attribute.currentStore", array('storeView' => $storeID));
    }

    /**
     * Allows you to retrieve the list of product attributes
     *
     * @access      public
     * @param       int set id
     * @return      array catalogAttributeEntity
    */
    public function productAttribute_list($setID) {
        return $this->mQuery("catalog_product_attribute.list", array('setId' => $setID));
    }

    /**
     * Allows you to retrieve the product attribute options
     *
     * @access      public
     * @param       string attribute id, *string store view id
     * @return      array catalogAttributeOptionEntity
    */
    public function productAttribute_options($attributeID, $storeID = null) {
        $data = array();
        $data['attributeId'] = $attributeID;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product_attribute.options", $data);
    }

    /**
     * Allows you to add a new option for attributes with selectable fields
     *
     * @access      public
     * @param       string attribute id, array catalogProductAttributeOptionEntityToAdd
     * @return      boolean result
    */
    public function productAttribute_addOption($attributeID, Array $data) {
        $data = array();
        $data['attribute'] = $attributeID;
        $data['data'] = $data;

        return $this->mQuery("product_attribute.addOption", $data);
    }

    /**
     * Allows you to create a new product attribute
     *
     * @access      public
     * @param       array catalogProductAttributeEntityToCreate
     * @return      int result
    */
    public function productAttribute_create(Array $data) {
        return $this->mQuery("product_attribute.create", array("data" => $data));
    }

    /**
     * Allows you to get full information about a required attribute with the list of options
     *
     * @access     public
     * @param      string attribute id
     * @return     array catalogProductAttributeEntity
    */
    public function productAttribute_info($attributeID) {
        return $this->mQuery("product_attribute.info", array('attribute' => $attributeID));
    }

    /**
     * Allows you to remove the required attribute from a product
     *
     * @access      public
     * @param       string attribute id
     * @return      boolean result
    */
    public function productAttribute_remove($attributeID) {
        return $this->mQuery("product_attribute.remove", array('attribute' => $attributeID));
    }

    /**
     * Allows you to remove the option for an attribute
     *
     * @access      public
     * @param       string attribute id, string option id
     * @return      boolean result
    */
    public function productAttribute_removeOption($attributeID, $optionID) {
        $data = array();
        $data['attribute'] = $attributeID;
        $data['optionId'] = $optionID;

        return $this->mQuery("product_attribute.removeOption", $data);
    }

    /**
     * Allows you to retrieve the list of possible attribute types
     *
     * @access      public
     * @param
     * @return      array catalogAttributeOptionEntity
    */
    public function productAttribute_types() {
        return $this->mQuery("product_attribute.types");
    }

    /**
     * Allows you to update the required attribute
     *
     * @access      public
     * @param       string attribute id, array catalogProductAttributeEntityToUpdate
     * @return      boolean result
    */
    public function productAttribute_update($attributeID, Array $data) {
        $data = array();
        $data['attribute'] = $attributeID;
        $data['data'] = $data;

        return $this->mQuery("product_attribute.update", $data);
    }


    # ***** product_attribute_set *****

    /**
     * Allows you to retrieve the list of product attribute sets
     *
     * @access      public
     * @param
     * @return      array catalogProductAttributeSetEntity
    */
    public function productAttributeSet_list() {
        return $this->mQuery("catalog_product_attribute_set.list");
    }

    /**
     * Allows you to add an existing attribute to an attribute set
     *
     * @access     public
     * @param      string attribute id, string attribute set id, *string attribute group id, *string sortOrder
     * @return     boolean result
    */
    public function productAttributeSet_attributeAdd($attributeID, $attributeSetID, $attributeGroupID = null, $sortOrder = null) {
        $data = array();
        $data['attributeId'] = $attributeID;
        $data['attributeSetId'] = $attributeSetID;
        $data['attributeGroupId'] = $attributeGroupID;
        $data['sortOrder'] = $sortOrder;

        return $this->mQuery("catalog_product_attribute_set.attributeAdd", $data);
    }

    /**
     * Allows you to remove an existing attribute from an attribute set
     *
     * @access      public
     * @param       string attribute id, string attribute set id
     * @return      boolean result
    */
    public function productAttributeSet_attributeRemove($attributeID, $attributeSetID) {
        $data = array();
        $data['attributeId'] = $attributeID;
        $data['attributeSetId'] = $attributeSetID;

        return $this->mQuery("product_attribute_set.attributeRemove", $data);
    }

    /**
     * Allows you to create a new attribute set based on another attribute set
     *
     * @access      public
     * @param       string attribute set name, string skeleton set id
     * @return      int set id
    */
    public function productAttributeSet_create($attributeSetName, $skeletonSetID) {
        $data = array();
        $data['attributeSetName'] = $attributeSetName;
        $data['skeletonSetId'] = $skeletonSetID;

        return $this->mQuery("product_attribute_set.create", $data);
    }

    /**
     * Allows you to add a new group for attributes to the attribute set
     *
     * @access      public
     * @param       string attribute set id, string group name
     * @return      int result
    */
    public function productAttributeSet_groupAdd($attributeSetID, $groupName) {
        $data = array();
        $data['attributeSetId'] = $attributeSetID;
        $data['groupName'] = $groupName;

        return $this->mQuery("product_attribute_set.groupAdd", $data);
    }

    /**
     * Allows you to remove a group from an attribute set
     *
     * @access      public
     * @param       string attribute group id
     * @return      boolean result
    */
    public function productAttributeSet_groupRemove($attributeGroupID) {
        return $this->mQuery("product_attribute_set.groupRemove", array('attributeGroupId' => $attributeGroupID));
    }

    /**
     * Allows you to rename a group in the attribute set
     *
     * @access      public
     * @param       string group id, string new name for group
     * @return      boolean result
    */
    public function productAttributeSet_groupRename($groupID, $groupName) {
        $data = array();
        $data['groupId'] = $groupID;
        $data['groupName'] = $groupName;

        return $this->mQuery("product_attribute_set.groupRename". $data);
    }

    /**
     * Allows you to remove an existing attribute set
     *
     * @access      public
     * @param       string attribute set id, *string force product remove flag
     * @return      boolean result
    */
    public function productAttributeSet_remove($attributeSetID, $forceProductsRemove = null) {
        $data = array();
        $data['attributeSetId'] = $attributeSetID;
        $data['forceProductsRemove'] = $forceProductsRemove;

        return $this->mQuery("product_attribute_set.remove", $data);
    }


    # ***** catalog_product_type *****

    /**
     * Allows you to retrieve the list of product types
     *
     * @access      public
     * @param
     * @return      array catalogProductTypeEntity
    */
    public function productCatalogType_list() {
        return $this->mQuery("catalog_product_type.list");
    }


    # ***** catalog_product_attribute_media *****

    /**
     * Allows you to set/get the current store view
     *
     * @access      public
     * @param       *string store view id
     * @return      int store view id
    */
    public function catalogProductAttributeMedia_currentStore($storeID = null) {
        return $this->mQuery("catalog_product_attribute_media.currentStore", array('storeView' => $storeID));
    }

    /**
     * Allows you to retrieve the list of product images
     *
     * @access      public
     * @param       string product id, *string store view id, *string product identifier type
     * @return
    */
    public function catalogProductAttributeMedia_list($productID, $storeID = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType !="sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product_attribute_media.list", $data);
    }

    /**
     * Allows you to retrieve information about the specified product image
     *
     * @access      public
     * @param       string product id, string name of the image file, *string store view id, *string product identifier type
     * @return      array catalogProductImageEntity
    */
    public function catalogProductAttributeMedia_info($productID, $file, $storeID = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType !="sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['file'] = $file;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product_attribute_media.info", $data);
    }

    /**
     * Allows you to retrieve product image types including standard image, small_image, thumbnail, etc.
     *
     * @access      public
     * @param       string id of the product attribute set
     * @return      array catalogProductAttributeMediaTypeEntity
    */
    public function catalogProductAttributeMedia_types($setID) {
        return $this->mQuery("catalog_product_attribute_media.types", array('setId' => $setID));
    }

    /**
     * Allows you to upload a new product image
     *
     * @access    public
     * @param     string product id, array catalogProductAttributeMediaCreateEntity, *string store view id, *string product identifier type
     * @return    string resulting image name
    */
    public function catalogProductAttributeMedia_create($productID, Array $data, $storeID = null, $productIdentifierType = "id") {
            if($productIdentifierType != "id" || $productIdentifierType !="sku")
                throw new Exception("Invalid productIdentifierType");

            $data = array();
            $data['product'] = $productID;
            $data['data'] = $data;
            $data['productIdentifierType'] = $productIdentifierType;
            $data['storeView'] = $storeID;

            return $this->mQuery("catalog_product_attribute_media.create", $data);
    }

    /**
     * Allows you to update the product image
     *
     * @access      public
     * @param       string product id, string image file name, array catalogProductAttributeMediaCreateEntity, *string store view id, *string product identifier
     * @return      boolean result
    */
    public function catalogProductAttributeMedia_update($productID, $file, Array $data, $storeID = null, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType !="sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['file'] = $file;
        $data['data'] = $data;
        $data['productIdentifierType'] = $productIdentifierType;
        $data['storeView'] = $storeID;

        return $this->mQuery("catalog_product_attribute_media.update", $data);
    }

    /**
     * Allows you to remove the image from a product
     *
     * @access      public
     * @param       string product id, string file image name, *string product identifier type
     * @return      boolean result
    */
    public function catalogProductAttributeMedia_remove($productID, $file, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType !="sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['file'] = $file;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_attribute_media.remove", $data);
    }


    # ***** catalog_product_attribute_tier_price *****

    /**
     * Allows you to retrieve information about product tier prices
     *
     * @access      public
     * @param       string product id, *string product identifier type
     * @return      array catalogProductTierPriceEntity
    */
    public function catalogProductAttributeTierPrice_info($productID, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_attribute_tier_price.info", $data);
    }

    /**
     * Allows you to update the product tier prices
     *
     * @access      public
     * @param       string product id, array catalogProductTierPriceEntity, *string product identifier type
     * @return      boolean result
    */
    public function catalogProductAttributeTierPrice_update($productID, Array $tierPrices, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['tierPrices'] = $tierPrices;
        $data['productIdentifiertype'] = $productIdentifierType;

        return $this->mQuery("catalog_product_attribute_tier_price.update", $data);
    }


    # ***** catalog_product_link *****

    /**
     * Allows you to retrieve the list of linked products for a specific product
     *
     * @access      public
     * @param       string link type, string product id, *string product identifier type
     * @return      array catalogProductLinkEntity
    */
    public function catalogProductLink_list($type, $productID, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        if(!in_array($type, array("cross_sell", "up_sell", "related", "grouped")))
            throw new Exception("Invalid type");

        $data = array();
        $data['type'] = $type;
        $data['productId'] = $productID;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_link.list", $data);
    }

    /**
     * Allows you to assign a product link (related, cross-sell, up-sell, or grouped) to another product
     *
     * @access      public
     * @param       string type of link, string product id, string linked product id, array catalogProductLinkEntity, *string product identifier type
     * @return      boolean result
    */
    public function catalogProductLink_assign($type, $productID, $linkedProductID, Array $data, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        if(!in_array($type, array("cross_sell", "up_sell", "related", "grouped")))
            throw new Exception("Invalid type");

        $data = array();
        $data['type'] = $type;
        $data['productId'] = $productID;
        $data['linkedProductId'] = $linkedProductID;
        $data['data'] = $data;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_link.assign", $data);
    }

    /**
     * Allows you to update the product link
     *
     * @access      public
     * @param       string type of link, string product id, string linked product id, array catalogProductLinkEntity, *string product identifier type
     * @return      boolean result
    */
    public function catalogProductLink_update($type, $productID, $linkedProductID, Array $data, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        if(!in_array($type, array("cross_sell", "up_sell", "related", "grouped")))
            throw new Exception("Invalid type");

        $data = array();
        $data['type'] = $type;
        $data['productId'] = $productID;
        $data['linkedProductId'] = $linkedProductID;
        $data['data'] = $data;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_link.update", $data);
    }

    /**
     * Allows you to remove the product link from a specific product
     *
     * @access      public
     * @param       string type of link, string product id, string linked product id, *string product identifier type
     * @return
    */
    public function catalogProductLink_remove($type, $productID, $linkedProductID, $productIdentifierType = "id") {
        if($productIdentifierType != "id" || $productIdentifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        if(!in_array($type, array("cross_sell", "up_sell", "related", "grouped")))
            throw new Exception("Invalid type");

        $data = array();
        $data['type'] = $type;
        $data['productId'] = $productID;
        $data['linkedProductId'] = $linkedProductID;
        $data['productIdentifierType'] = $productIdentifierType;

        return $this->mQuery("catalog_product_link.update", $data);
    }

    /**
     * Allows you to retrieve the list of product link types
     *
     * @access      public
     * @param
     * @return      array of link types
    */
    public function catalogProductLink_types() {
        return $this->mQuery("catalog_product_link.types");
    }

    /**
     * Allows you to retrieve the product link type attributes
     *
     * @access      public
     * @param       string type of link
     * @return      array catalogProductLinkAttributeEntity
    */
    public function catalogProductLink_attributes($type) {
        if(!in_array($type, array("cross_sell", "up_sell", "related", "grouped")))
            throw new Exception("Invalid type");

        return $this->mQuery("catalog_product_link.attributes", array('type' => $type));
    }


    # ***** product_downloadable_link *****

    /**
     * Allows you to add a new link to a downloadable product
     *
     * @access      public
     * @param       string product id, array catalogProductDownloadableLinkAddEntity, *string resourcetype, *string store view id, *string identifier type
     * @return      int result
    */
    public function productDownloadableLink_add($productID, Array $resource, $resourceType = "link", $storeID = null, $identifierType = "id") {
        if($identifierType != "id" || $identifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        if($resourceType != "sample" || $resourceType != "link")
            throw new Exception("Invalid resourceType");

        $data = array();
        $data['productId'] = $productID;
        $data['resource'] = $resource;
        $data['resourceType'] = $resourceType;
        $data['identifierType'] = $identifierType;
        $data['store'] = $storeID;

        return $this->mQuery("product_downloadable_link.add", $data);
    }

    /**
     * Allows you to retrieve a list of links of a downloadable product
     *
     * @access      public
     * @param       string product id, *string store view id, *string identifier type
     * @return      array catalogProductDownloadablelinkListEntity
    */
    public function productDownloadableLink_list($productID, $storeID = null, $identifierType = "id") {
        if($identifierType != "id" || $identifierType != "sku")
            throw new Exception("Invalid productIdentifierType");

        $data = array();
        $data['productId'] = $productID;
        $data['identifierType'] = $identifierType;
        $data['store'] = $storeID;

        return $this->mQuery("product_downloadable_link.list", $data);
    }

    /**
     * Allows you to remove a link/sample from a downloadable product
     *
     * @access      public
     * @param       string link id, string resource type
     * @return
    */
    public function productDownloadableLink_remove($linkID, $resourceType = "link") {
        if($resourceType != "sample" || $resourceType != "link")
            throw new Exception("Invalid resourceType");

        $data = array();
        $data['linkId'] = $linkID;
        $data['resourceType'] = $resourceType;

        return $this->mQuery("product_downloadable_link.remove", $data);
    }


    # ***** product_tag *****

    /**
     * Allows you to add one or more tags to a product
     *
     * @access      public
     * @param       array catalogProducttagAddEntity
     * @return      array tag name => tag id
    */
    public function productTag_add(Array $data) {
        return $this->mQuery("catalog_product_tag.add", array('data' => $data));
    }

    /**
     * Allows you to retrieve information about the required product tag
     *
     * @access      public
     * @param       string tag id, string store view id
     * @return      array catalogProductTagInfoEntity
    */
    public function productTag_info($tagID, $storeID) {
        $data = array();
        $data['tagId'] = $tagID;
        $data['store'] = $storeID;

        return $this->mQuery("catalog_product_tag.info", $data);
    }

    /**
     * Allows you to retrieve the list of tags for a specific product
     *
     * @access      public
     * @param       string product id, string store view id
     * @return      array catalogProductTagListEntity
    */
    public function productTag_list($productID, $storeID) {
        $data = array();
        $data['productId'] = $productID;
        $data['store'] = $storeID;

        return $this->mQuery("catalog_product_tag.list", $data);
    }

    /**
     * Allows you to update information about an existing product tag
     *
     * @access      public
     * @param       string tag id, array catalogProductTagUpdateEntity, *string store view id
     * @return      boolean result
    */
    public function productTag_update($tagID, Array $data, $storeID = null) {
        $data = array();
        $data['tagId'] = $tagID;
        $data['data'] = $data;
        $data['store'] = $storeID;

        return $this->mQuery("catalog_product_tag.update", $data);
    }

    /**
     * Allows you to remove an existing product tag
     *
     * @access      public
     * @param       string tag id
     * @return      boolean result
    */
    public function productTag_remove($tagID) {
        return $this->mQuery("catalog_product_tag.remove", array('tagId' => $tagID));
    }


    # ***** product_custom_option *****

    /**
     * Allows you to add a new custom option for a product
     *
     * @access      public
     * @param       string option id, array catalogProductCustomOptionToUpdate, *string store view id
     * @return      boolean result
    */
    public function productCustomOption_add($optionID, Array $data, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['data'] = $data;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option.update", $data);
    }

    /**
     * Allows you to update the required product custom option
     *
     * @access      public
     * @param       string option id, array catalogProductCustomOptionToUpdate, *string store view id
     * @return      boolean result
    */
    public function productCustomOption_update($optionID, Array $data, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['data'] = $data;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option.update", $data);
    }

    /**
     * Allows you to retrieve the list of available custom option types
     *
     * @access      public
     * @param
     * @return      array catalogProductCustomOptionTypes
    */
    public function productCustomOption_types() {
        return $this->mQuery("product_custom_option.types");
    }

    /**
     * Allows you to retrieve the list of custom options for a specific product
     *
     * @access      public
     * @param       string option id, *string store view id
     * @return      array catalogProductCustomOptionList
    */
    public function productCustomOption_list($optionID, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option.list", $data);
    }

    /**
     * Allows you to retrieve full information about the custom option in a product
     *
     * @access      public
     * @param       string option id, *string store view id
     * @return      array catalogProductCustomOptionInfoEntity
    */
    public function productCustomOption_info($optionID, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option.info", $data);
    }

    /**
     * Allows you to remove a custom option from the product
     *
     * @access      public
     * @param       string custom option id
     * @return      boolean result
    */
    public function productCustomOption_remove($optionID) {
        return $this->mQuery("product_custom_option.remove", array("optionId" => $optionID));
    }


    # ***** product_custom_option_value *****

    /**
     * Allows you to add a new custom option value to a custom option
     *
     * @access      public
     * @param       string option id, array catalogProductCustomOptionValueAdd, *string store view id
     * @return      boolean result
    */
    public function productCustomOptionValue_add($optionID, Array $data, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['data'] = $data;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option_value.add", $data);
    }

    /**
     * Allows you to retrieve the list of product custom option values
     *
     * @access      public
     * @param       string option id, *string store view id
     * @return      array catalogProductCustomOptionValueList
    */
    public function productCustomOptionValue_list($optionID, $storeID = null) {
        $data = array();
        $data['optionId'] = $optionID;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option_value.list", $data);
    }

    /**
     * Allows you to retrieve full information about the specified product custom option value
     *
     * @access      public
     * @param       string value id, *string store view id
     * @return      array catalogProductCustomOptionValueInfoEntity
    */
    public function productCustomOptionValue_info($valueID, $storeID = null) {
        $data = array();
        $data['valueId'] = $valueID;
        $data['store'] = $storeID;

        return $this->mQuery("product_custom_option_value.info", $data);
    }

    /**
     * Allows you to update the product custom option value
     *
     * @access      public
     * @param       string value id, array catalogProductCustomOptionValueUpdateEntity, *string store view id
     * @return      boolean result
    */
    public function productCustomOptionValue_update($valueID, Array $data, $storeID = null) {
        $data = array();
        $data['valueId'] = $valueID;
        $data['data'] = $data;
        $data['storeId'] = $storeID;

        return $this->mQuery("product_custom_option_value.update", $data);
    }

    /**
     * Allows you to remove the custom option value from a product
     *
     * @access      public
     * @param       string custom option value id
     * @return      boolean result
    */
    public function productCustomOptionValue_remove($valueID) {
        return $this->mQuery("product_custom_option_value.remove", array("valueId" => $valueID));
    }


    # ***** cart_coupon *****

    /**
     * Allows you to add a coupon code for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, string coupon code, *string store view id
     * @return      boolean result
    */
    public function cartCoupon_add($quoteID, $couponCode, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['couponCode'] = $couponCode;
        $data['store'] = $storeID;

        return $this->mQuery("cart_coupon.add", $data);
    }

    /**
     * Allows you to remove a coupon code from a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      boolean result
    */
    public function cartCoupon_remove($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart_coupon.remove", $data);
    }


    # ***** cart_customer *****

    /**
     * Allows you to add information about the customer to a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartCustomerEntity, *string store view id
     * @return      boolean result
    */
    public function cartCustomer_set($quoteID, Array $customerData, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['customerData'] = $customerData;
        $data['store'] = $storeID;

        return $this->mQuery("cart_cutsomer.set", $data);
    }

    /**
     * Allows you to set the customer addresses in the shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartCustomerAddressEntity, *string store view id
     * @return      boolean result
    */
    public function cartCustomer_addresses($quoteID, Array $customerAddressData, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['customerAddressData'] = $customerAddressData;
        $data['store'] = $storeID;

        return $this->mQuery("cart_customer.addresses", $data);
    }


    # ***** cart_payment *****

    /**
     * Allows you to set a payment method for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartPaymentMethodEntity, *string store view id
     * @return      boolean result
    */
    public function cartPayment_method($quoteID, Array $paymentData, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['paymentData'] = $paymentData;
        $data['store'] = $storeID;

        return $this->mQuery("cart_payment.method", $data);
    }

    /**
     * Allows you to retrieve a list of available payment methods for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartPyamentMethodResponseEntity
    */
    public function cartPayment_list($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart_payment.list", $data);
    }


    # ***** cart_product *****

    /**
     * Allows you to add one or more products to the shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartProductEntity, *string store view id
     * @return      boolean result
    */
    public function cartProduct_add($quoteID, Array $products, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['productsData'] = $products;
        $data['storeId'] = $storeID;

        return $this->mQuery("cart_product.add", $data);
    }

    /**
     * Allows you to update one or several products in the shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartProductEntity, *string store view id
     * @return      boolean result
    */
    public function cartProduct_update($quoteID, Array $products, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['productsData'] = $products;
        $data['store'] = $storeID;

        return $this->mQuery("cart_product.update", $data);
    }

    /**
     * Allows you to remove one or several products from a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartProductEntity, *string store view id
     * @return      boolean result
    */
    public function cartProduct_remove($quoteID, Array $products, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['productsData'] = $products;
        $data['store'] = $storeID;

        return $this->mQuery("cart_product.remove", $data);
    }

    /**
     * Allows you to retrieve the list of products in the shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartProductresponseEntity
    */
    public function cartProduct_list($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart_product.list", $data);
    }

    /**
     * Allows you to move products from the current quote to a customer quote
     *
     * @access      public
     * @param       int shopping cart id, array shoppingCartProductEntity, *string store view id
     * @return      boolean result
    */
    public function cartProduct_moveToCustomerQuote($quoteID, Array $products, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['productsData'] = $products;
        $data['store'] = $storeID;

        return $this->mQuery("cart_product.moveToCustomerQuote", $data);
    }


    # ***** cart_shipping *****

    /**
     * Allows you to retrieve the list of available shipping methods for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, string shipping method code, *string store view id
     * @return      boolean result
    */
    public function cartShipping_method($quoteID, $shippingMethod, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['shippingMethod'] = $shippingMethod;
        $data['store'] = $storeID;

        return $this->mQuery("cart_shipping.method", $data);
    }

    /**
     * Allows you to set a shipping method for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartShippingMethodEntity
    */
    public function cartShipping_list($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart_shipping.list", $data);
    }


    # ***** cart *****

    /**
     * Allows you to create an empty shopping cart
     *
     * @access     public
     * @param      *string storeId
     * @return     int empty cart ID
    */
    public function cart_create($storeID = null) {
        return $this->mQuery("cart.create", array('storeId' => $storeID));
    }

    /**
     * Allows you to create an order from a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string storeViewID, *array website license id's
     * @return      string result
    */
    public function cart_order($quoteID, $storeID = null, Array $agreements = array()) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;
        $data['agreements'] = $agreements;

        return $this->mQuery("cart.order", $data);
    }

    /**
     * Allows you to retrieve full information about the shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartInfoEntity
    */
    public function cart_info($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart.info", $data);
    }

    /**
     * Allows you to retrieve total prices for a shopping cart
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartTotalsEntity
    */
    public function cart_totals($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart.totals", $data);
    }

    /**
     * Allows you to retrieve the website license agreement for the quote according to the website
     *
     * @access      public
     * @param       int shopping cart id, *string store view id
     * @return      array shoppingCartLicenseEntity
    */
    public function cart_license($quoteID, $storeID = null) {
        $data = array();
        $data['quoteId'] = $quoteID;
        $data['store'] = $storeID;

        return $this->mQuery("cart.license", $data);
    }


    # ***** customer ******

    /**
     * Allows you to retrieve the list of customers
     *
     * @access      public
     * @param       *array customer attribute filters
     * @return      array customerCustomerEntity
    */
    public function customer_list(Array $filters = array()) {
        return $this->mQuery("customer.list", array('filters' => $filters));
    }


    /**
     * Allows you to export/import customers from/to Magento
     *
     * @access      public
     * @param       array customerData
     * @return      int customerID
    */
    public function customer_create(Array $customerData) {
        return $this->mQuery("customer.create", array("customerData" => $customerData));
    }


    /**
     * Update information about the required customer
     *
     * @access      public
     * @param       int customerID, array customerData
     * @return      boolean
    */
    public function customer_update($customerID, Array $customerData) {
        $data = array();
        $data['customerId'] = $customerID;
        $data['customerData'] = $customerData;

        return $this->mQuery("customer.update", $data);
    }

    /**
     * Delete the required customer
     *
     * @access      public
     * @param       int customerID
     * @return      boolean
    */
    public function customer_delete($customerID) {
        return $this->mQuery("customer.delete", array("customerId" => $customerID));
    }

    /**
     * Retrieve information about the specified customer
     *
     * @access      public
     * @param       int customerID, *array attribute filters
     * @return      array customerCustomerEntity
    */
    public function customer_info($customerID, Array $filters = array()) {
        $data = array();
        $data['customerId'] = $customerID;
        $data['filters'] = $filters;

        return $this->mQuery("customer.info", $data);
    }


    # ***** customer_group *****

    /**
     * Retrieve the list of customer groups
     *
     * @access      public
     * @param
     * @return      array customerGroupEntity
    */
    public function customerGroup_list() {
        return $this->mQuery("customer_group.list");
    }


     # ***** customer_address *****

    /**
     * Retrieve the list of customer addresses
     *
     * @access      public
     * @param       int customerID
     * @return      array customerAddressEntity
    */
    public function customerAddress_list($customerID) {
        return $this->mQuery("customer_address.list", array("customerId" => $customerID));
    }

    /**
     * Create a new address for the customer
     *
     * @access      public
     * @param       int customerID, array customerAddressEntityCreate
     * @return      int result
    */
    public function customerAddress_create($customerID, Array $addressData) {
        $data = array();
        $data['customerId'] = $customerID;
        $data['addressData'] = $addressData;

        return $this->mQuery("customer_address.create", $data);
    }

    /**
     * Update address data of the required customer
     *
     * @access     public
     * @param      int addressID, array customerAddressEntityCreate
     * @return     boolean result
    */
    public function customerAddress_update($addressID, Array $addressData) {
        $data = array();
        $data['addressId'] = $addressID;
        $data['addressData'] = $addressData;

        return $this->mQuery("customer_address.update", $data);
    }

    /**
     * Delete the required customer address
     *
     * @access     public
     * @param      int addressId
     * @return     boolean result
    */
    public function customerAddress_delete($addressID) {
        return $this->mQuery("customer_address.delete", array("addressId" => $addressID));
    }

    /**
     * Retrieve information about the required customer address
     *
     * @access      public
     * @param       int addressId
     * @return      array customerAddressEntityItem
    */
    public function customerAddress_info($addressID) {
        return $this->mQuery("customer_address.info", array("addressId" => $addressID));
    }


    # ***** sales_order *****

    /**
     * Allows you to retrieve the list of orders
     *
     * @access     public
     * @param      *array filters
     * @return     array salesOrderEntity
    */
    public function salesOrder_list(Array $filters = array()) {
        return $this->mQuery("sales_order.list", array("filters" => $filters));
    }

    /**
     * Allows you to retrieve the required order information
     *
     * @access      public
     * @param       string orderIncrementId
     * @return      array salesOrderEntity
    */
    public function salesOrder_info($orderIncrementID) {
        return $this->mQuery("sales_order.info", array("orderIncrementId" => $orderIncrementID));
    }

    /**
     * Allows you to add a new comment to the order
     *
     * @access      public
     * @param       string orderIncrementId, string status, *string comment, *string notification flag
     * @return      boolean result
    */
    public function salesOrder_addComment($orderIncrementID, $status, $comment = null, $notify = null) {
        $data = array();
        $data['orderIncrementId'] = $orderIncrementID;
        $data['status'] = $status;
        $data['notify'] = $notify;
        $data['comment'] = $comment;

        return $this->mQuery("sales_order.addComment", $data);
    }

    /**
     * Allows you to place the required order on hold
     *
     * @access      public
     * @param       string orderIncrementID
     * @return      boolean result
    */
    public function salesOrder_hold($orderIncrementID) {
        return $this->mQuery("sales_order.hold", array("orderIncrementId" => $orderIncrementID));
    }

    /**
     * Allows you to unhold the required order
     *
     * @access      public
     * @param       string orderIncrementID
     * @return      boolean result
    */
    public function salesOrder_unhold($orderIncrementID) {
        return $this->mQuery("sales_order.unhold", array("orderIncrementId" => $orderIncrementID));
    }

    /**
     * Allows you to cancel the required order
     *
     * @access      public
     * @param       string orderIncrementID
     * @return      boolean result
    */
    public function salesOrder_cancel($orderIncrementID) {
        return $this->mQuery("sales_order.cancel", array("orderIncrementId" => $orderIncrementID));
    }


    # ***** sales_order_invoice *****

    /**
     * Allows you to retrieve the list of order invoices
     *
     * @access      public
     * @param       *array filters
     * @return      array salesOrderInvoiceEntity
    */
    public function salesOrderInvoice_list(Array $filters = array()) {
        return $this->mQuery("sales_order_invoice.list", array('filters' => $filters));
    }

    /**
     * Allows you to retrieve information about the required invoice
     *
     * @access      public
     * @param       string invoiceIncrementId
     * @return      array salesOrderInvoiceEntity
    */
    public function salesOrderInvoice_info($invoiceIncrementID) {
        return $this->mQuery("sales_order_invoice.info", array("invoiceIncrementId" => $invoiceIncrementID));
    }

    /**
     * Allows you to create a new invoice for an order
     *
     * @access      public
     * @param       string orderincrementid, *array itemsQty, *string comment, *string sendemail, *string includecomment
     * @return      string invoice id
    */
    public function salesOrderInvoice_create($orderIncrementId, Array $itemsQty = array(), $comment = null, $email = null, $includeComment = null) {
        $data = array();
        $data['orderIncrementId'] = $orderIncrementId;
        $data['email'] = $email;
        $data['includeComment'] = $includeComment;
        $data['itemsQty'] = $itemsQty;
        $data['comment'] = $comment;

        return $this->mQuery("sales_order_invoice.create", $data);
    }

    /**
     * Allows you to add a new comment to the order invoice
     *
     * @access      public
     * @param       string invoiceIncrementId, *string comment, *int send email, *int include comment in email
     * @return      boolean result
    */
    public function salesOrderInvoice_addComment($invoiceIncrementID, $comment = null, $email = 0, $includeComment = 0) {
        $data = array();
        $data['invoiceIncrementId'] = $invoiceIncrementID;
        $data['email'] = $email;
        $data['includeComment'] = $includeComment;
        $data['comment'] = $comment;

        return $this->mQuery("sales_order_invoice.addComment", $data);
    }

    /**
     * Allows you to capture the required invoice
     *
     * @access      public
     * @param       string invoiceIncrementId
     * @return      boolean result
    */
    public function salesOrderInvoice_capture($invoiceIncrementID) {
        return $this->mQuery("sales_order_invoice.capture", array("invoiceIncrementId" => $invoiceIncrementID));
    }

    /**
     * Allows you to cancel the required invoice
     *
     * @access      public
     * @param       string invoiceIncrementID
     * @return      boolean result
    */
    public function salesOrderInvoice_cancel($invoiceIncrementID) {
        return $this->mQuery("sales_order_invoice.cancel", array("invoiceIncrementId" => $invoiceIncrementID));
    }


    # ***** sales_order_shipment *****

    /**
     * Allows you to retrieve the list of order shipments
     *
     * @access      public
     * @param       array filters
     * @return      array salesOrderShipmentEntity
    */
    public function salesOrderShipment_list(Array $filters = array()) {
        return $this->mQuery("sales_order_shipment.list", array("filters" => $filters));
    }

    /**
     * Allows you to retrieve the shipment information
     *
     * @access      public
     * @param       string shipmentIncrementID
     * @return      array salesOrderShipmentEntity
    */
    public function salesOrderShipment_info($shipmentIncrementID) {
        return $this->mQuery("sales_order_shipment.info", array("shipmentIncrementId" => $shipmentIncrementID));
    }

    /**
     * Allows you to create a new shipment for an order
     *
     * @access      public
     * @param       string orderincrementid, *array itemsQty, *string comment, *int sendEmail, *int includeComment
     * @return      string shipmentIncrementID
    */
    public function salesOrderShipment_create($orderIncrementID, Array $itemsQty = array(), $comment = null, $sendEmail = 0, $includeComment = 0) {
        $data = array();
        $data['email'] = $sendEmail;
        $data['includeComment'] = $includeComment;
        $data['itemsQty'] = $itemsQty;
        $data['comment'] = $comment;

        return $this->mQuery("sales_order_shipment.create", $data);
    }

    /**
     * Allows you to add a new comment to the order shipment
     *
     * @access      public
     * @param       string shipmentIncrementID, *string comment, *string sendEmail, *string include comment in email
     * @return
    */
    public function salesOrderShipment_addComment($shipmentIncrementID, $comment = null, $email = null, $includeInEmail = null) {
        $data = array();
        $data['shipmentIncrementId'] = $shipmentIncrementID;
        $data['email'] = $email;
        $data['includeInEmail'] = $includeInEmail;
        $data['comment'] = $comment;

        return $this->mQuery("sales_order_shipment.addComment", $data);
    }

    /**
     * Allows you to add a new tracking number to the order shipment
     *
     * @access      public
     * @param       string shipmentIncrementID, string carrier code, string tracking title, string tracking number
     * @return      int tracking number ID
    */
    public function salesOrderShipment_addTrack($shipmentIncrementID, $carrier, $title, $trackNumber) {
        if(!in_array($carrier, array("ups", "usps", "dhl", "fedex", "dhlint")))
            throw new Exception("Invalid carrier");

        $data = array();
        $data['shipmentIncrementId'] = $shipmentIncrementID;
        $data['carrier'] = $carrier;
        $data['title'] = $title;
        $data['trackNumber'] = $trackNumber;

        return $this->mQuery("sales_order_shipment.addTrack", $data);

    }

    /**
     * Allows you to remove a tracking number from the order shipment
     *
     * @access      public
     * @param       string shipmentIncrementId, string trackId
     * @return      boolean result
    */
    public function salesOrderShipment_removeTrack($shipmentIncrementID, $trackID) {
        $data = array();
        $data['shipmentIncrementId'] = $shipmentIncrementID;
        $data['trackId'] = $trackID;

        return $this->mQuery("sales_order_shipment.removeTrack", $data);
    }

    /**
     * Allows you to retrieve the list of allowed carriers for an order
     *
     * @access      public
     * @param       string orderIncrementID
     * @return      array carriers
    */
    public function salesOrderShipment_getCarriers($orderIncrementID) {
        return $this->mQuery("sales_order_shipment.getCarriers", array("orderIncrementId" => $orderIncrementID));
    }


    # ***** sales_order_creditmemo *****

    /**
     * Allows you to retrieve the list of credit memos by filters
     *
     * @access      public
     * @param       *array filters
     * @return      array salesOrderCreditmemoEntity
    */
    public function salesOrderCreditmemo_list(Array $filters = array()) {
        return $this->mQuery("order_creditmemo.list", $filters);
    }

    /**
     * Allows you to retrieve full information about the specified credit memo
     *
     * @access      public
     * @param       string creditmemo increment id
     * @return      array salesOrderCreditmemoEntity
    */
    public function salesOrderCreditmemo_info($creditmemoIncrementID) {
        return $this->mQuery("order_creditmemo.info", array("creditmemoIncrementId" => $creditmemoIncrementID));
    }

    /**
     * Allows you to create a new credit memo for the invoiced order
     *
     * @access      public
     * @param       string creditmemoincrementid, *array salesOrderCreditmemoData, *string comments, *int notifycustomer, *int includecomments, *string refund amount
     * @return      string result
    */
    public function salesOrderCreditmemo_create($orderIncrementID, Array $creditmemoData = array(), $comment = null, $notifyCustomer = 0, $includeComment = 0, $refundToStoreCreditAmount = null) {
        $data = array();
        $data['notifyCustomer'] = $notifyCustomer;
        $data['includeComment'] = $includeComment;
        $data['orderIncrementId'] = $orderIncrementID;
        $data['creditmemoData'] = $creditmemoData;
        $data['comment'] = $comment;
        $data['refundToStoreCreditAmount'] = $refundToStoreCreditAmount;

        return $this->mQuery("order_creditmemo.create", $data);
    }

    /**
     * Allows you to add a new comment to an existing credit memo
     *
     * @access      public
     * @param       string creditmemoincrementid, *string comment, *int notifycustomer, *int includecomment
     * @return      boolean result
    */
    public function salesOrderCreditmemo_addComment($creditmemoIncrementId, $comment = null, $notifyCustomer = 0, $includeComment = 0) {
        $data = array();
        $data['notifyCustomer'] = $notifyCustomer;
        $data['includeComment'] = $includeComment;
        $data['creditmemoIncrementId'] = $creditmemoIncrementId;
        $data['comment'] = $comment;

        return $this->mQuery("order_creditmemo.addComment", $data);
    }

    /**
     * Allows you to cancel an existing credit memo
     *
     * @access      public
     * @param       string creditmemoincrementID
     * @return      string result of cancel
    */
    public function salesOrderCreditmemo_cancel($credit_memo_increment_id) {
        return $this->mQuery("order_creditmemo.cancel", array("creditmemoIncrementId" => $credit_memo_increment_id));
    }


    # ***** cataloginventory_stock_item *****

    /**
     * Allows you to retrieve the list of stock data by product IDs
     *
     * @access      public
     * @param       array productIDs/SKUs
     * @return      array catlogInvetoryStockItemEntity
    */
    public function catalogInventoryStockItem_list(Array $productFilter = array()) {
        return $this->mQuery("cataloginventory_stock_item.list", array('productIds' => $productFilter));
    }

    /**
     * Allows you to update the required product stock data
     *
     * @access      public
     * @param       string productID/SKU, array catalogInventoryStockItemUpdateEntity
     * @return      int result of update
    */
    public function catalogInventoryStockItem_update($productID, Array $data) {
        $data = array();
        $data['productId'] = $productID;
        $data['data'] = $data;

        return $this->mQuery("cataloginventory_stock_item.update", $data);
    }
}
