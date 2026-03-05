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
use Magento\Backend\Block\Widget\Form\Generic;

class PasswordVerification
{
    private const string XML_PATH_ENABLE_ADMIN = 'hoodoor/general/enable_admin';

    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    )
    {
    }

    /** Remove password and verification fields from the admin form when Hoodoor is enabled. */
    public function remove(Generic $subject): void
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

    /** Check whether Hoodoor password-less login is enabled for admin. */
    private function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(self::XML_PATH_ENABLE_ADMIN);
    }

    /** Remove the password and confirmation fields from the base fieldset. */
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

    /** Remove the current-user verification fieldset from the form. */
    private function removeVerificationFieldset(DataForm $form): void
    {
        $elements = $form->getElements();
        $this->unsetByIds(
            $elements,
            ['current_user_verification_fieldset']
        );
        $form->setElements($elements);
    }

    /** Remove elements from a form collection by their IDs. */
    private function unsetByIds(ElementCollection $collection, array $ids): void
    {
        foreach ($collection as $key => $elem) {
            if (in_array((string)$elem->getId(), $ids, true)) {
                unset($collection[$key]);
            }
        }
    }
}
