<?php

namespace Opengento\Hoodoor\Test\Unit\Processor;

use Magento\Email\Model\Template;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\TransportInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Opengento\Hoodoor\Api\RequestLoginRepositoryInterface;
use Opengento\Hoodoor\Enum\Config;
use Opengento\Hoodoor\Model\LoginRequest;
use Opengento\Hoodoor\Processor\EmailProcessor;
use Opengento\Hoodoor\Service\JwtManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class EmailProcessorTest extends TestCase
{
    /** @var MockObject|RequestLoginRepositoryInterface */
    private $loginRequestRepositoryMock;

    /** @var MockObject|ScopeConfigInterface */
    private $scopeConfigMock;

    /** @var MockObject|TransportBuilder */
    private $transportBuilderMock;

    /** @var MockObject|StateInterface */
    private $inlineTranslationMock;

    /** @var MockObject|LoggerInterface */
    private $loggerMock;

    /** @var MockObject|JwtManager */
    private $jwtManagerMock;

    /** @var EmailProcessor */
    private $emailProcessor;

    protected function setUp(): void
    {
        $this->loginRequestRepositoryMock = $this->createMock(RequestLoginRepositoryInterface::class);
        $this->scopeConfigMock = $this->createMock(ScopeConfigInterface::class);
        $this->transportBuilderMock = $this->createMock(TransportBuilder::class);
        $this->inlineTranslationMock = $this->createMock(StateInterface::class);
        $this->loggerMock = $this->createMock(LoggerInterface::class);
        $this->jwtManagerMock = $this->createMock(JwtManager::class);

        $this->emailProcessor = new EmailProcessor(
            $this->loginRequestRepositoryMock,
            $this->scopeConfigMock,
            $this->transportBuilderMock,
            $this->inlineTranslationMock,
            $this->loggerMock,
            $this->jwtManagerMock
        );
    }

    public function testSendMailSuccessfully()
    {
        $to = 'customer@magento.test';
        $type = 'frontend';

        $loginRequestMock = $this->createMock(LoginRequest::class);
        $loginRequestMock->expects($this->once())->method('getEmail')->willReturn($to);
        $loginRequestMock->expects($this->once())->method('getToken')->willReturn('test_token');

        $this->loginRequestRepositoryMock->expects($this->once())
            ->method('get')
            ->with($to)
            ->willReturn($loginRequestMock);

        $this->scopeConfigMock->method('getValue')
            ->willReturnMap([
                [Config::XML_PATH_HOODOOR_TEMPLATE_ID->value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, 'template_id'],
                [Config::XML_PATH_HOODOOR_SENDER_EMAIL->value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, 'sender@magento.test'],
                [Config::XML_PATH_HOODOOR_SENDER_NAME->value, ScopeConfigInterface::SCOPE_TYPE_DEFAULT, null, 'Sender Name']
            ]);

        $this->jwtManagerMock->expects($this->once())
            ->method('generateToken')
            ->with(['email' => $to, 'token' => 'test_token'], 900)
            ->willReturn('jwt_token');

        $this->inlineTranslationMock->expects($this->once())->method('suspend');

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateIdentifier')
            ->with('template_id')
            ->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateModel')
            ->with(Template::class)
            ->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateOptions')
            ->with([
                'area' => Area::AREA_FRONTEND,
                'store' => Store::DEFAULT_STORE_ID
            ])
            ->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())
            ->method('setTemplateVars')
            ->with(['type' => 'frontend', 'request' => 'jwt_token'])
            ->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())
            ->method('setFromByScope')
            ->with(['email' => 'sender@magento.test', 'name' => 'Sender Name'], ScopeInterface::SCOPE_STORE)
            ->willReturnSelf();

        $this->transportBuilderMock->expects($this->once())
            ->method('addTo')
            ->with($to)
            ->willReturnSelf();

        $transportMock = $this->createMock(TransportInterface::class);
        $this->transportBuilderMock->expects($this->once())
            ->method('getTransport')
            ->willReturn($transportMock);

        $transportMock->expects($this->once())->method('sendMessage');

        $this->inlineTranslationMock->expects($this->once())->method('resume');

        $this->emailProcessor->sendMail($to, $type);
    }
}
