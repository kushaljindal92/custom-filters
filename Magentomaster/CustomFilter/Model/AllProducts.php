<?php
namespace Magentomaster\CustomFilter\Model;

use Psr\Log\LoggerInterface;
use Magento\Customer\Model\Session as CustomerSession;

class AllProducts 
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
        \Magento\Framework\App\ResourceConnection $resource,
        CustomerSession $customerSession
    ) {
        $this->resource = $resource;
        $this->customerSession = $customerSession;
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
                $allIds[] =  $item['product_id'];
            }
            return $allIds;
        }
    }
}