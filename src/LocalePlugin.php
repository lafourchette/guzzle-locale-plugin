<?php
namespace Guzzle\Plugin\Locale;

use Guzzle\Common\Event;
use Guzzle\Service\Command\OperationCommand;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LocalePlugin implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return array('command.before_send' => 'onBeforeSend');
    }

    public function onBeforeSend(Event $event)
    {
        if (isset($event['command'])
            && $event['command'] instanceof OperationCommand
        ) {
            /** @var OperationCommand $command */
            $command = $event['command'];
            //If command contain a locale parameter, append it to the query
            if ($command->hasKey('locale')) {
                $command->getRequest()
                    ->getQuery()
                    ->set(
                        '_locale',
                        $command->get('locale')
                    );
            }
        }
    }
}
