<?php
namespace Concrete\Package\CommunityStoreMailjetSubscribing\Src\Event;

use Concrete\Core\Support\Facade\Application;
use Concrete\Core\Support\Facade\Log;
use \Mailjet\Resources;
class Order
{
    private $apiKey;
    private $apiSecret;

    public function orderPaymentComplete($event)
    {
        $order = $event->getOrder();
        if ($order) {
            $this->getOrderInfo($order);
        }
    }

    private function getOrderInfo($order)
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $this->apiKey = $config->get('mailjet_subscribing.apiKey');
        $this->apiSecret = $config->get('mailjet_subscribing.apiSecret');
        $listID=$config->get('mailjet_subscribing.listID');
        
        $customerInfo = array();
        $customerInfo['email'] = $order->getAttribute('email');
        $customerInfo['firstName'] = $order->getAttribute('billing_first_name');
        $customerInfo['lastName'] = $order->getAttribute('billing_last_name');

        // Create a Mailjet client
        $mailjet = new \Mailjet\Client($this->apiKey, $this->apiSecret, true, ['version' => 'v3']);

        // Create the request to add the contact
        $response = $mailjet->post(
            Resources::$Contact, 
            [
                'body' => [
                    'IsExcludedFromCampaigns' => "false",
                    'Email' => $customerInfo['email'] ,
                    'Name' => $customerInfo['firstName'] . ' ' . $customerInfo['lastName']

                ]
            ]
        );
        $resp_arr=$response->getData();
        Log::addError(serialize($response->getData()));
        if($response->success() && $listID!=''){

            /* Add addtional fields to contacts start */
            // Create the request to add the contact
            $body = [
                'Data' => [
                  [
                    'Name' => "country",
                    'Value' => $order->getAttribute('shipping_address')->getFullCountry()
                  ],
                  [
                    'Name' => "firstname",
                    'Value' => $customerInfo['lastName']
                  ],
                  [
                    'Name' => "name",
                    'Value' =>  $customerInfo['firstName'] . ' ' . $customerInfo['lastName']
                  ],
                  [
                    'Name' => "newsletter_sub",
                    'Value' => "Y"
                  ]
                ]
              ];
              $response = $mailjet->put(Resources::$Contactdata, ['id' =>$resp_arr[0]['ID'], 'body' => $body]);
            /* Add additional fields to contact end */
            $body = [
                'IsUnsubscribed' => "false",
                'ContactID' => $resp_arr[0]['ID'],
                'ContactAlt' =>$customerInfo['email'],
                'ListID' => $listID,
               
              ];
              $response = $mailjet->post(Resources::$Listrecipient, ['body' => $body]);
              Log::addError(serialize($response->getData()));
             // $response->success() && var_dump($response->getData());
        }
       

        // Check the response status
        if ($response->success()) {
            Log::addError($customerInfo['email']." Contact added successfully!");
        } else {
            Log::addError($customerInfo['email']." Failed to add contact. Error: " . $response->getData());
        }

    }
  
}
