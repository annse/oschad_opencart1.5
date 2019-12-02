<form action="<?php echo $action; ?>" method="post" id="payment" name='cardform' accept-charset="windows-1251">
	<input name="LANG" type="hidden" value="" />
    <input name="TRTYPE" type="hidden" value="<?php echo $trtype; ?>" />
	<input name="ORDER" type="hidden" value="<?php echo $order_id; ?>" />
	<input name="DESC" type="hidden" value="<?php echo $desc; ?>" />
    <input name="AMOUNT" type="hidden" value="<?php echo $amount; ?>" />
    <input name="CURRENCY" type="hidden" value="<?php echo $uah_code; ?>" />
	<input name="MERCH_NAME" type="hidden" value="<?php echo $merch_name; ?>" />
    <input name="MERCH_URL" type="hidden" value="<?php echo $merch_url; ?>" />
    <input name="TERMINAL" type="hidden" value="<?php echo $terminal_id; ?>" />
    <input name="MERCHANT" type="hidden" value="<?php echo $merchant_id; ?>" />
	<input name="EMAIL" type="hidden" value="<?php echo $email; ?>" />
	<input name="NONCE" type="hidden" value="<?php echo $nonce; ?>" />
    <input name="TIMESTAMP" type="hidden" value="<?php echo $time; ?>" />
	<input name="BACKREF" type="hidden" value="<?php echo $backref; ?>" />	
    <input name="P_SIGN" type="hidden" value="<?php echo $sign; ?>" />
</form>
<div class="buttons">
	<div class="right"><a onclick="$('#payment').submit();" class="button"><span><?php echo $button_confirm; ?></span></a></div>
</div>