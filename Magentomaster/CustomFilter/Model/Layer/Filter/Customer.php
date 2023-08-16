<?php
namespace Magentomaster\CustomFilter\Model\Layer\Filter;

use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class Customer extends \Magento\Catalog\Model\Layer\Filter\AbstractFilter
{
   
    /**
     * @var \Psr\Log\LoggerInterface
     */
    protected $logger;
    protected $resource;
    protected $connection;

    /**
     * Construct
     *
     * @param \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Magento\Catalog\Model\Layer $layer
     * @param \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder
     * @param \Magento\Framework\Escaper $escaper
     * @param CategoryFactory $categoryDataProviderFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Catalog\Model\Layer\Filter\ItemFactory $filterItemFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Catalog\Model\Layer $layer,
        \Magento\Catalog\Model\Layer\Filter\Item\DataBuilder $itemDataBuilder,
        LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource,
        CustomerSession $customerSession
    ) {
        parent::__construct($filterItemFactory, $storeManager, $layer, $itemDataBuilder);
        $this->_requestVar = 'customer_id';
        $this->logger = $logger;
        $this->resource = $resource;
        $this->customerSession = $customerSession;
    }

    /**
     * Apply category filter to layer
     *
     * @param   \Magento\Framework\App\RequestInterface $request
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    { 
        //echo count($this->getAllIds());
        $customer_id = null;
        if ($this->customerSession->isLoggedIn()) {
            $customer_id =  $this->customerSession->getCustomer()->getId();
        }else{
            return $this;
        }

        $collection = $this->getLayer()->getProductCollection();
        
        $collection->getSelect()
        ->joinLeft(['cp'=>'customer_price'], 'e.entity_id = cp.product_id')
        ->where('cp.customer_id = '.$customer_id);
        //echo $collection->getSelect()->__toString(); die;
        $this->getLayer()->setProductCollection($collection);
        $this->getLayer()
        ->getState()
        ->addFilter(
            $this->_createItem($customer_id,$customer_id) //add dynamic customer id here
        );
        
        return $this;
    }

    /**
     * Get filter name
     *
     * @return \Magento\Framework\Phrase
     */
    public function getName()
    {
        return __('Customer');
    }

    public function getAllIds(){
        $allIds = [];
        if ($this->customerSession->isLoggedIn()) {
            $customer_id =  $this->customerSession->getCustomer()->getId();
            $connection = $this->resource->getConnection();
            $tableName = $this->resource->getTableName('customer_price'); // Replace with your table name

            $select = $connection->select()
                ->from($tableName,['product_id'])
                ->where('customer_id = ?', $customer_id); // Replace column_name with your actual column name

            $items = $connection->fetchAll($select);
            foreach($items as $item){
                if($item['product_id'] != 'all'){
                    $allIds[] =  $item['product_id'];
                }
            }
            return $allIds;
        }
    }
}