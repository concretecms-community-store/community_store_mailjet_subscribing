<?php
namespace Concrete\Package\CommunityStoreMailjetSubscribing;

use Concrete\Core\Support\Facade\Application;
use Events;
use Package;
use SinglePage;
use Whoops\Exception\ErrorException;

class Controller extends Package
{
    protected $pkgHandle = 'community_store_mailjet_subscribing';
    protected $appVersionRequired = '5.7.5';
    protected $pkgVersion = '1.0';

    public function getPackageName()
    {
        return t('Mailjet Subscribing');
    }

    public function getPackageDescription()
    {
        return t('Subscribe Community Store customers to Mailjet lists based on products purchased.');
    }

    public function install()
    {
        $installed = Package::getInstalledHandles();
        if (!(is_array($installed) && in_array('community_store', $installed)) ) {
            throw new ErrorException(t('This package requires that Community Store be installed'));
        }

        $pkg = parent::install();
        $sp = SinglePage::add('/dashboard/store/mailjet_subscribing', $pkg);
        if (is_object($sp)) {
            $sp->update(array('cName' => t('Mailjet Subscribing')));
        }
    }

    public function on_start()
    {
        require $this->getPackagePath() . '/vendor/autoload.php';
        $app = Application::getFacadeApplication();
        $config = $config = $app->make('config');
        $enableSubscriptions = $config->get('mailjet_subscribing.enableSubscriptions');
        if ($enableSubscriptions) {
            $orderlistener = $app->make('\Concrete\Package\CommunityStoreMailjetSubscribing\Src\Event\Order');
            Events::addListener('on_community_store_payment_complete', array($orderlistener, 'orderPaymentComplete'));
        }
    }
    
}
