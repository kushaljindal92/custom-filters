<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Magentomaster\CustomFilter\Plugin\Magento\Catalog\Model\Layer;

class FilterList
{
    const CUSTOMER  = 'customer';

    protected $filterTypes = [
       self::CUSTOMER => \Magentomaster\CustomFilter\Model\Layer\Filter\Customer::class,
    ];
    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager
    ) {
        $this->objectManager = $objectManager;
    }

    public function afterGetFilters(
        \Magento\Catalog\Model\Layer\FilterList $subject,
        $result,
        $layer
    ) {
        $result[] = $this->objectManager->create($this->filterTypes[self::CUSTOMER], ['layer' => $layer]);
        return $result;
    }
}

