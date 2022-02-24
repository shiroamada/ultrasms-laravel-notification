<?php

namespace NotificationChannels\UltraSms\Test;

use NotificationChannels\UltraSms\UltraSmsMessage;

class UltraSmsMessageTest extends \PHPUnit_Framework_TestCase
{
    /** @test */
    public function it_can_accept_a_content_when_constructing_a_message()
    {
        $message = new UltraSmsMessage('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_accept_a_content_when_creating_a_message()
    {
        $message = UltraSmsMessage::create('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_content()
    {
        $message = (new UltraSmsMessage())->content('hello');

        $this->assertEquals('hello', $message->content);
    }

    /** @test */
    public function it_can_set_the_send_at()
    {
        $sendAt = date_create();
        $message = (new UltraSmsMessage())->sendAt($sendAt);

        $this->assertEquals($sendAt, $message->sendAt);
    }
}
