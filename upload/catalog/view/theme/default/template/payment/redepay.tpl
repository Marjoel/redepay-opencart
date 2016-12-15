<style>
	#sair-container .modal {
		display: block!important;
	}
</style>

<div class="buttons" id="redepay">
	<div class="pull-right">
		<script src="<?php echo $script; ?>" data-publishable-key="<?php echo $public_key; ?>" data-image="cen1_hor_op3_pc_225x45" data-order-id="" id="redepay-script"></script>
	</div>
</div>

<script type="text/javascript"><!--
	$.ajax({
		url: '<?php echo $post; ?>',
		type: 'post',
		dataType: 'json',
		success: function(json) {
			$('#redepay-script').attr('data-order-id', json.id);
			
			<?php if($auto_start) { ?>
				setTimeout(function() {
					RedePay.start();
				}, (<?php echo $delay; ?>*1000));
			<?php } ?>
		}
	});
//--></script>