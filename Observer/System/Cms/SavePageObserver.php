<?php

declare(strict_types=1);

namespace Web200\Seo\Observer\System\Cms;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class SavePageObserver implements ObserverInterface
{
    /**
     * @param Observer $observer
     *
     * @return void
     */
    public function savePage($observer)
    {
        $model = $observer->getEvent()->getPage();
        $request = $observer->getEvent()->getRequest();
        $data = $request->getPost();

        if ($data['open_graph_image_url']) {
            $model->setOpenGraphUrl($data['open_graph_image_url']);
        }
    }


    public function execute(Observer $observer)
    {
        $this->savePage($observer);
    }
}
