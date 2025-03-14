<?php
namespace Concrete\Package\CommunityStoreMailjetSubscribing\Controller\SinglePage\Dashboard\Store;

use Concrete\Core\Page\Controller\DashboardPageController;
use Concrete\Core\Support\Facade\Application;

class MailjetSubscribing extends DashboardPageController
{
    public function view()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');
        $this->set('enableSubscriptions', $config->get('mailjet_subscribing.enableSubscriptions'));
        $this->set('apiKey', $config->get('mailjet_subscribing.apiKey'));
        $this->set('apiSecret', $config->get('mailjet_subscribing.apiSecret'));
        $this->set('listID', $config->get('mailjet_subscribing.listID'));
    }

    public function settings_saved()
    {
        $this->set('message', t('Settings Saved'));
        $this->view();
    }

    public function save_settings()
    {
        $app = Application::getFacadeApplication();
        $config = $app->make('config');

        if ($this->post()) {
            if ($this->token->validate('save_settings')) {
                $enableSubscriptions = $this->request->post('enableSubscriptions');
                $apiKey = $this->request->post('apiKey');
                $apiSecret = $this->request->post('apiSecret');
                $listID = $this->request->post('listID');

                if ($enableSubscriptions) {
                    if (!$apiKey) {
                        $this->error->add(t('An API Key is required'));
                    }
                    if (!$apiSecret) {
                        $this->error->add(t('An API Secret is required'));
                    }
                }

                $config->save('mailjet_subscribing.enableSubscriptions', $enableSubscriptions);
                $config->save('mailjet_subscribing.apiKey', $apiKey);
                $config->save('mailjet_subscribing.apiSecret', $apiSecret);
                $config->save('mailjet_subscribing.listID', $listID);

                if (!$this->error->has()) {
                    $this->redirect('/dashboard/store/mailjet_subscribing', 'settings_saved');
                }
            } else {
                $this->error->add(t('Invalid CSRF token. Please refresh and try again.'));
                $this->view();
            }
        }
    }
}
