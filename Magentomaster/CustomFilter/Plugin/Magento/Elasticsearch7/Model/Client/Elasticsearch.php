<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magentomaster\CustomFilter\Plugin\Magento\Elasticsearch7\Model\Client;

use Magentomaster\CustomFilter\Model\AllProducts;

class Elasticsearch
{
    protected $customer;

    public function __construct(AllProducts $customer){
        $this->customer = $customer;
    }

    public function beforeQuery(
        \Magento\Elasticsearch7\Model\Client\Elasticsearch $subject,$query
    ) {
        $filteredIds = $this->customer->getAllIds();
        if(!$filteredIds || count($filteredIds) < 1)  {
            return [$query]; 
        }
    
        // Add the product ids to filter the Elasticsearch product collection      
        $query['body']['query']['bool']['filter'] = ['ids' => [ 'values' => $filteredIds]];
        return [$query];
    }
}

