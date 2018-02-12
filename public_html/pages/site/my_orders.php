<div class="order_banner"><img src="pics/track_banner.jpg" width="100%" /></div>

<?
	if ($this->data->user->loged_in_user){
		$user = $this->data->user->loged_in_user;

		$orders = $user->get_orders();

		if (!empty($orders)){
			foreach ($orders as $order){
			?>
			<div class="track_table">
				<div><span class="track_name">Order date:</span> <span><?=date("d-m-Y", strtotime($order->created_date));?></span></div>
				<div><span class="track_name">Order price:</span> <span><?=$order->final_price?></span></div>
				<div><span class="track_name">Tracking code:</span>
                <span> <a target="_blank" href="https://tools.usps.com/go/TrackConfirmAction.action?tRef=fullpage&tLc=1&text28777=&tLabels=<?=$order->tracking_code?>">
                 <?=$order->tracking_code?></a> </span>
                 </div>
			</div>
			<?
			}
		}
	}
?>
