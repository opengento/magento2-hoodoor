<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\PasswordLessLogin\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Widget\Button as WidgetButton;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Opengento\PasswordLessLogin\Enum\Config;

class Button extends Field
{
    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function _getElementHtml(AbstractElement $element): string
    {
        $value = $this->_scopeConfig->getValue(Config::XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY->value);
        $element->setType('password')->setValue($value)->setReadonly(true);
        $html = parent::_getElementHtml($element);
        return $html . $this->getButtonHtml() . $this->getJs($element);
    }

    /**
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    protected function getButtonHtml()
    {
        $button = $this->getLayout()->createBlock(WidgetButton::class)
            ->setData(array(
                'id' => 'generate_secret_key',
                'label' => __('Generate Secret Key'),
                'class' => 'generate-secret-key-button'
            ));

        return $button->toHtml();
    }

    /**
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function getJs(AbstractElement $element): string
    {
        $url = $this->getUrl(Config::XML_PATH_PASSWORDLESSLOGIN_SECRET_KEY->value);
        return '
            <script>
                require(["jquery", "domReady!"], function($) {
                    $("#generate_secret_key").click(function() {
                        $.ajax({
                            url: "'. $url .'",
                            type: "get",
                            dataType: "json",
                            success: function (data) {
                                $("#' . $element->getHtmlId() . '").val(data.secret_key);
                            }
                        });
                    })
                })
            </script>
        ';
    }
}
