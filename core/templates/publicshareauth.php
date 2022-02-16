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
		<div class="warning-info" id="email-prompt" style="display:none;"><?php p($l->t('Please type in your email address to request a temporary password')); ?></div>
		<p id="password-input">
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
		 <p id="email-input" style="display:none;">
			<input type="email" id="email" name="identityToken" placeholder="<?php p($l->t('Email address')); ?>" />
			<input type="submit" id="password-request" name="passwordRequest" class="svg icon-confirm input-button-inline" value="" /> 
		</p>
		<?php if (isset($_['identityOk'])): ?>
			<?php if ($_['identityOk']): ?>
				<div class="warning-info" id="identification-success"><?php p($l->t('Password sent!')); ?></div>
			<?php endif; ?>
			<?php if (!$_['identityOk']): ?>
				<div class="warning-info" id="identification-failure"><?php p($l->t('You are not authorized to request a password for this share')); ?></div>
			<?php endif; ?>
		<?php endif; ?>
	</fieldset>
</form>
<?php if ($_['share']->getShareType()===$_['share']::TYPE_EMAIL && !$_['share']->getSendPasswordByTalk()): ?>
	<input type="button"
		id="request-password-button-not-talk"
		value="<?php p($l->t('Request password')); ?>"
		class="primary" />
<?php endif; ?>
