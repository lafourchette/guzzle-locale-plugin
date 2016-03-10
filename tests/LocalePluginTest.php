<?php
namespace Guzzle\Plugin\Locale;

use Guzzle\Common\Event;
use Guzzle\Http\Message\RequestInterface;
use Guzzle\Http\QueryString;
use Guzzle\Service\Command\OperationCommand;
use Prophecy\Prophecy\ObjectProphecy;

class LocalePluginTest extends \PHPUnit_Framework_TestCase
{
    /** @var LocalePlugin */
    private $plugin;

    public function setUp()
    {
        $this->plugin = new LocalePlugin();
    }

    public function testGetSubscribedEvents()
    {
        $this->assertSame(
            array('command.before_send' => 'onBeforeSend'),
            LocalePlugin::getSubscribedEvents()
        );
    }

    /**
     * @dataProvider getTestOnBeforeSendData
     *
     * @param boolean $hasLocale
     */
    public function testOnBeforeSend($hasLocale)
    {
        $locale = 'it_IT';

        /** @var Event|ObjectProphecy $event */
        $event = $this->prophesize('Guzzle\Common\Event');
        /** @var OperationCommand|ObjectProphecy $command */
        $command = $this->prophesize('Guzzle\Service\Command\OperationCommand');
        /** @var RequestInterface|ObjectProphecy $request */
        $request = $this->prophesize('Guzzle\Http\Message\RequestInterface');
        /** @var QueryString|ObjectProphecy $query */
        $query = $this->prophesize('Guzzle\Http\QueryString');
        
        $event->offsetExists('command')
            ->willReturn(true)
            ->shouldBeCalledTimes(1);

        $event->offsetGet('command')
            ->willReturn($command->reveal())
            ->shouldBeCalledTimes(2);

        $command->hasKey('locale')
            ->willReturn($hasLocale)
            ->shouldBeCalledTimes(1);

        $command->getRequest()
            ->willReturn($request->reveal())
            ->shouldBeCalledTimes($hasLocale ? 1 : 0);
        $command->get('locale')
            ->willReturn($locale)
            ->shouldBeCalledTimes($hasLocale ? 1 : 0);

        $request->getQuery()
            ->willReturn($query->reveal())
            ->shouldBeCalledTimes($hasLocale ? 1 : 0);

        $query->set('_locale', $locale)
            ->shouldBeCalledTimes($hasLocale ? 1 : 0);

        $this->plugin->onBeforeSend($event->reveal());
    }

    public function getTestOnBeforeSendData()
    {
        return array(
            'with locale' => array(
                'hasLocale' => true,
            ),
            'without locale' => array(
                'hasLocale' => false,
            ),
        );
    }
}
