<?php defined('C5_EXECUTE') or die('Access Denied.');
$app = \Concrete\Core\Support\Facade\Application::getFacadeApplication();
?>

<form method="post" action="<?php echo $this->action('save_settings'); ?>">
    <?php $app->make('token')->output('save_settings'); ?>

    <div class="form-group">
        <div class="checkbox">
            <label>
            <?php
            echo $form->checkbox('enableSubscriptions', 1, $enableSubscriptions);
            echo t('Enable Product List Subscriptions');
            ?>
            </label>
        </div>
    </div>

    <div class="form-group">
        <?php echo $form->label('apiKey', t('API Key')); ?>
        <?php echo $form->text('apiKey', $apiKey); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('apiSecret', t('API Secret')); ?>
        <?php echo $form->text('apiSecret', $apiSecret); ?>
    </div>

    <div class="form-group">
        <?php echo $form->label('listID', t('List ID')); ?>
        <?php echo $form->text('listID', $listID); ?>
        <span class="help-text"><?php t('Customers will be added to this list on transaction completion, regardless of product purchases'); ?></span>
    </div>

    <div class="ccm-dashboard-form-actions-wrapper">
        <div class="ccm-dashboard-form-actions">
            <button class="pull-right btn btn-primary" type="submit"><?php echo t('Save'); ?></button>
        </div>
    </div>
</form>
