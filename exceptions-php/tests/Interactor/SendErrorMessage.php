<?php

namespace EEException\Test\Interactor;

require_once(realpath(__DIR__) . '/../../src/Notifier/Airbrake.php');
require_once(realpath(__DIR__) . '/../../src/UseCases/SendErrorString.php');

class TestSendErrorMessage extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    public function testReturnsBoolean()
    {
        $notifier = $this->_getNotifierMock();
        $interactor = new \EEException\UseCases\SendErrorString($notifier);

        $result = $interactor->execute('Message');
        $this->assertInternalType('boolean', $result);
    }

    public function testSendsExceptionThroughNotifier()
    {
        $expectedMessage = 'Message';
        $expectedOrigin = 'Origin';

        $notifier = $this->_getNotifierMock();
        $notifier->expects($this->once())->method('SendErrorString')->with($expectedMessage);

        $interactor = new \EEException\UseCases\SendErrorString($notifier);

        $result = $interactor->execute($expectedMessage, $expectedOrigin);
        $this->assertInternalType('boolean', $result);
    }

    public function testMessageMustBeString()
    {
        $notifier = $this->_getNotifierMock();
        $interactor = new \EEException\UseCases\SendErrorString($notifier);

        $this->setExpectedException('InvalidArgumentException', 'Message');
        $interactor->execute(true);
    }

    public function testMessageMustNotBeBlank()
    {
        $notifier = $this->_getNotifierMock();
        $interactor = new \EEException\UseCases\SendErrorString($notifier);

        $this->setExpectedException('InvalidArgumentException', 'Message');
        $interactor->execute('');
    }

    protected function _getNotifierMock()
    {
        $notifier = $this->getMock('EEException\Notifier\Airbrake', array(), array(array('apiKey' => 'FAKE')));

        $notifier->expects($this->any())
            ->method('SendErrorString')
            ->withAnyParameters()
            ->will($this->returnValue(true));

        return $notifier;
    }
}
