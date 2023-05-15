<?php

namespace NotificationChannels\UltraSms;

use DomainException;
use GuzzleHttp\Client as HttpClient;
use NotificationChannels\UltraSms\Exceptions\CouldNotSendNotification;

class UltraSmsApi
{
    const FORMAT_JSON = 3;

    /** @var string */
    protected $apiUrl = 'https://api.ultramsg.com/';

    /** @var HttpClient */
    protected $httpClient;

    /** @var string */
    protected $instanceId;

    /** @var string */
    protected $token;

    /** @var integer */
    public $isMalaysiaMode;

    /** @var integer */
    public $isEnable;

    /** @var integer */
    public $isDebug;

    /** @var string */
    public $debugReceiveNumber;


    /** @var string */
    protected $action = '/messages/chat';

    /** @var string */
    protected $priority = '10';


    public function __construct($config)
    {
        $this->instanceId = $config['instanceId'];
        $this->token = $config['token'];

        $this->isMalaysiaMode = $config['isMalaysiaMode'];
        $this->isEnable = (isset($config['isEnable']) ? $config['isEnable'] : 0);
        $this->isDebug = $config['isDebug'];
        $this->debugReceiveNumber = $config['debugReceiveNumber'];


        $this->httpClient = new HttpClient([
            'base_uri' =>  $this->apiUrl.$this->instanceId.$this->action,
            'timeout' => 8.0,
        ]);
    }

    /**
     * @param  array  $params
     *
     * @return array
     *
     * @throws CouldNotSendNotification
     */
    public function send($params)
    {
        if($this->isEnable)
        {
            try {
                $sendsms_url = "?token={$this->token}&priority={$this->priority}&to={$params['to']}&body={$params['mesg']}";

                $response = $this->httpClient->request('GET', $this->apiUrl.$this->instanceId.$this->action.$sendsms_url);

                $stream = $response->getBody();

                $content = $stream->getContents();

                $response = json_decode((string) $response->getBody(), true);

                return $response;
            } catch (DomainException $exception) {
                throw CouldNotSendNotification::exceptionUltraSmsRespondedWithAnError($exception);
            } catch (\Exception $exception) {
                throw CouldNotSendNotification::couldNotCommunicateWithUltraSms($exception);
            }
        }
    }
}
