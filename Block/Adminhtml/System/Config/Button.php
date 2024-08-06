<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button as WidgetButton;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Opengento\Hoodoor\Enum\Config;

class Button extends Field
{
    /**
     * Create Element Html (Button + Script)
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $value = $this->_scopeConfig->getValue(Config::XML_PATH_HOODOOR_SECRET_KEY->value);
        $element->setType('password')->setValue($value)->setReadonly(true);
        $html = parent::_getElementHtml($element);
        return $html . $this->getButtonHtml() . $this->getJs($element);
    }

    /**
     * Create Button Block
     *
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getButtonHtml(): string
    {
        $button = $this->getLayout()->createBlock(WidgetButton::class)
            ->setData([
                'id' => 'generate_secret_key',
                'label' => __('Generate Secret Key'),
                'class' => 'generate-secret-key-button'
            ]);

        return $button->toHtml();
    }

    /**
     * Generate Secret Key Script
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function getJs(AbstractElement $element): string
    {
        return '
            <script>
                require(["jquery", "domReady!"], function($) {
                    $("#generate_secret_key").click(function() {
                      $("#' . $element->getHtmlId() . '").val(window.crypto.randomUUID());
                    })
                })
            </script>
        ';
    }
}
