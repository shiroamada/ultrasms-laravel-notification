<?php

namespace NotificationChannels\UltraSms\Test;

use Illuminate\Notifications\Notification;
use Mockery as M;
use NotificationChannels\UltraSms\Exceptions\CouldNotSendNotification;
use NotificationChannels\UltraSms\UltraSmsApi;
use NotificationChannels\UltraSms\UltraSmsChannel;
use NotificationChannels\UltraSms\UltraSmsMessage;

class UltraSmsChannelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UltraSmsApi
     */
    private $ultrasms;

    /**
     * @var UltraSmsMessage
     */
    private $message;

    /**
     * @var UltraSmsChannel
     */
    private $channel;

    /**
     * @var \DateTime
     */
    public static $sendAt;

    public function setUp()
    {
        parent::setUp();

        $config = [
            'instanceId'=> 'instaceId',
            'token'     => 'token',
        ];

        $this->ultrasms = M::mock(UltraSmsApi::class, $config);
        $this->channel = new UltraSmsChannel($this->ultrasms);
        $this->message = M::mock(UltraSmsMessage::class);
    }

    public function tearDown()
    {
        M::close();

        parent::tearDown();
    }

    /** @test */
    public function it_can_send_a_notification()
    {
        $this->ultrasms->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotification());
    }

    /** @test */
    public function it_can_send_a_deferred_notification()
    {
        self::$sendAt = new \DateTime();

        $this->ultrasms->shouldReceive('send')->once()
            ->with(
                [
                    'to'  => '60123456789',
                    'body'     => 'hello',
                    'time'    => '0'.self::$sendAt->getTimestamp(),
                ]
            );

        $this->channel->send(new TestNotifiable(), new TestNotificationWithSendAt());
    }

    /** @test */
    public function it_does_not_send_a_message_when_to_missed()
    {
        $this->expectException(CouldNotSendNotification::class);

        $this->channel->send(
            new TestNotifiableWithoutRouteNotificationForSmscru(), new TestNotification()
        );
    }
}

class TestNotifiable
{
    public function routeNotificationFor()
    {
        return '0123456789';
    }
}

class TestNotifiableWithoutRouteNotificationForSmscru extends TestNotifiable
{
    public function routeNotificationFor()
    {
        return false;
    }
}

class TestNotification extends Notification
{
    public function toUltraSms()
    {
        return UltraSmsMessage::create('hello');
    }
}

class TestNotificationWithSendAt extends Notification
{
    public function toUltraSms()
    {
        return UltraSmsMessage::create('hello')
            ->sendAt(UltraSmsChannelTest::$sendAt);
    }
}
