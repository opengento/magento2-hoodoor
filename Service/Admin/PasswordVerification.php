<?php
/**
 * Copyright © OpenGento, All rights reserved.
 * See LICENSE bundled with this library for license details.
 */
declare(strict_types=1);

namespace Opengento\Hoodoor\Service\Admin;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Form as DataForm;
use Magento\Framework\Data\Form\Element\Collection as ElementCollection;
use Magento\Framework\Data\Form\Element\Fieldset;
use Magento\Backend\Block\System\Account\Edit\Form as AccountForm;
use Magento\User\Block\User\Edit\Tab\Main as UserMain;
use Magento\User\Block\Role\Tab\Info as RoleInfo;

class PasswordVerification
{
    private const XML_PATH_ENABLE_ADMIN = 'hoodoor/general/enable_admin';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    )
    {
    }

    public function remove(AccountForm|UserMain|RoleInfo $subject): void
    {
        if (!$this->isEnabled()) {
            return;
        }

        $form = $subject->getForm();
        if (!$form instanceof DataForm) {
            return;
        }

        $this->removePasswordFields($form);
        $this->removeVerificationFieldset($form);

        $subject->setForm($form);
    }

    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_ADMIN);
    }

    private function removePasswordFields(DataForm $form): void
    {
        /** @var Fieldset|null $baseFs */
        $baseFs = $form->getElement('base_fieldset');
        if (!$baseFs instanceof Fieldset) {
            return;
        }
        $this->unsetByIds(
            $baseFs->getElements(),
            ['password', 'confirmation']
        );
    }

    private function removeVerificationFieldset(DataForm $form): void
    {
        $elements = $form->getElements();
        $this->unsetByIds(
            $elements,
            ['current_user_verification_fieldset']
        );
        $form->setElements($elements);
    }

    private function unsetByIds(ElementCollection $collection, array $ids): void
    {
        foreach ($collection as $key => $elem) {
            if (in_array((string)$elem->getId(), $ids, true)) {
                unset($collection[$key]);
            }
        }
    }
}
