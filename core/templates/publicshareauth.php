<?php
	/** @var array $_ */
	/** @var \OCP\IL10N $l */
	style('core', 'guest');
	style('core', 'publicshareauth');
	script('core', 'publicshareauth');
?>
<form method="post">
	<fieldset class="warning">
		<?php if (!isset($_['wrongpw'])): ?>
			<div class="warning-info"><?php p($l->t('This share is password-protected')); ?></div>
		<?php endif; ?>
		<?php if (isset($_['wrongpw'])): ?>
			<div class="warning"><?php p($l->t('The password is wrong. Try again.')); ?></div>
		<?php endif; ?>
		<p>
			<label for="password" class="infield"><?php p($l->t('Password')); ?></label>
			<input type="hidden" name="requesttoken" value="<?php p($_['requesttoken']) ?>" />
			<input type="password" name="password" id="password"
				placeholder="<?php p($l->t('Password')); ?>" value=""
				autocomplete="new-password" autocapitalize="off" autocorrect="off"
				autofocus />
			<input type="hidden" name="sharingToken" value="<?php p($_['share']->getToken()) ?>" id="sharingToken">
			<input type="hidden" name="sharingType" value="<?php p($_['share']->getShareType()) ?>" id="sharingType">
			<input type="submit" id="password-submit" 
				class="svg icon-confirm input-button-inline" value="" disabled="disabled" />
		</p>
	</fieldset>
</form>
<?php if ($_['share']->getShareType()==$_['share']::TYPE_EMAIL && !$_['share']->getSendPasswordByTalk()): ?>
<form method="post">
	<input type="button" id="password-request" value="<?php p($l->t('Request password')); ?>" class="primary button-vue" />
</form>
<?php endif; ?>
