<div class="wrap">
	<h2>All for Adsense Framework</h2>
	<form method="post" action="options.php">
		<?php
			settings_fields('afa_options'); //settings group name
		?>
	<h3><?php _e('Choose your preferred script for AdSense', 'all-adsense'); ?></h3>
	<div class="well">
	<?php _e('Click on ', 'all-adsense'); ?>
	<a data-toggle="modal" data-target="#myModal" href="#"><?php _e('Search'); ?></a>
	<?php _e(' to find all providers adhering to standards NoBlock', 'all-adsense'); ?>
	</div>

	<br>
	<div class="input-group control-group">
		<span class="input-group-addon" id="basic-addon1">Adsense Library</span>
		<input type="text" class="form-control" name="afa[lib]" id="lib" placeholder="javascript file" value="<?php echo $this->p['lib']; ?>" required>
		<span class="input-group-addon" for="name">
			<a data-toggle="modal" data-target="#myModal" href="#"><?php _e('Search'); ?></a>
		</span>
	</div>
	<!-- Modal Search-->
	<div class="modal fade" id="myModal" role="dialog">
		<div class="modal-dialog">
		  <!-- Modal content-->
		  <div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal">&times;</button>
			  <h4 class="modal-title">Search Adsense Library</h4>
			</div>
			<div class="modal-body">
				<p>Seeking a library that</p>
				<div class="input-group">
					<input type="checkbox" id="ban" name="ban" value="true" /> <?php _e('protects the account from ban by blocking fraudulent clicks'); ?><br>
					<input type="checkbox" id="adb" name="adb" value="true" /> <?php _e('increases earnings forcing AdBlocker to disable AdBlock'); ?><br/>
				</div>
				<br>

				<style>
				.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
					  background-color: #337ab7;
					  color: white;
				}
				tr.item{
					cursor: pointer;
				}
				</style>
				<table class="table table-striped table-hover">
					<thead>
					  <tr>
						<th class="col-md-6"><?php _e('Description'); ?></th>
						<th class="col-md-2">Script</th>
						<th class="col-md-1"><?php _e('Policy'); ?></th>
					  </tr>
					</thead>
					<tbody id="results">
					  <tr>
						<td>Ads by Google</td>
						<td>//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js</td>
						<td><a target="_blank" href="https://support.google.com/adsense/answer/23921">read</a></td>
					  </tr>
					</tbody>
				</table>

			</div>
			<div class="modal-footer">
				<div class="row">
					<div class="col-md-6">Click on the library for use it</div>
					<div class="col-md-6"><span class="pull-right"><button id="close" type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Close'); ?></button></span></div>
				</div>
			</div>
		  </div>
		</div>
	</div>



































	<br>
	<h3>Page-level ads</h3>
	<div class="well">
	<?php _e('If you want to use the ', 'all-adsense'); ?>
	<a target="_blank" href="https://support.google.com/adsense/answer/6245307">Page-level ads</a>
	<?php _e(', to increase Adsense revenue on mobile devices, insert here your code, otherwise leave it blank', 'all-adsense'); ?>
	</div>

	<div class="form-group">
	  <label for="comment">Page-level code:</label>
	  <textarea placeholder='<script>
(adsbygoogle = window.adsbygoogle || []).push({
google_ad_client: "ca-pub-1234567890123456",
enable_page_level_ads: true
});
</script>' class="form-control" rows="7" name="afa[code]" id="code" ><?php echo $this->p['code']; ?></textarea>
	</div>















	<br>
	<h3><?php _e('Increase the speed'); ?></h3>
	<div class="input-group">
		<input name="afa[async]" type="checkbox" id="async" value="1" <?php checked('1', $this->p['async']); ?> /> <?php _e('The script will be run asynchronously to boost the site performance'); ?>
	</div>		










	<br>
	<h3><?php _e('No yellow'); ?></h3>
	<div class="input-group">
		<input name="afa[no_yellow]" type="checkbox" id="no_yellow" value="1" <?php checked('1', $this->p['no_yellow']); ?> /> <?php _e('Removes yellow background from ads'); ?>
	</div>




























	
<div style="width:580px">

	<p class="submit">
	<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
	</p>
</div>

	</form>
</div>


















<!--
<br>
<h3>Compatibility</h3>
<p><?php
/*
printf( '
	<div id="ratemessage" class="notice notice-info">
		<p>
			<strong>%1$s</strong>
			%2$s
			<a href="javascript:alladsense_rate_frontend();" class="button">%4$s</a>
		</p>
	</div>',
	__( 'All For Adsense:', 'all-adsense' ),
	__( 'is compatible with the majority of Adsense plugins. Here you can test the compatibility' ),
	esc_js( wp_create_nonce( 'all-adsense-ignore' ) ),
	__( 'Test' )
);
*/
?></p>
-->























<script type='text/javascript'>
(function($) {
	$('#myModal').on('show.bs.modal', function (event) {
		var modal = $(this);
		var search_ban = true;
		var search_adb = true;
		modal.find('#ban').prop('checked', search_ban);
		modal.find('#adb').prop('checked', search_adb);
		function showResults() {
			$("tr.item").each(function() {
				var tr = $(this)
				var r_ban = (tr.attr('ban') == '1');
				var r_adb = (tr.attr('adb') == '1');
				if((r_ban == search_ban) && (r_adb == search_adb)){
					tr.show();
				}else{
					tr.hide();
				}
			});
		}
		var r = '';
		<?php
			reset($this->lb);
			while (list($key, $value) = each($this->lb)) {
		?>
		r += '<tr ban="<?php if($value['ban']) {echo '1';}else{echo '0';} ?>" adb="<?php if($value['adblock']) {echo '1';}else{echo '0';} ?>" h="<?php echo $value['async']; ?>" class="item"><td><?php echo $value['desc']; ?></td><td><?php 
		$url = $value['async'];
		echo parse_url($url, PHP_URL_HOST) . ' ... ' . basename($url); ?></td><td><a target="_blank" class="policy" href="<?php echo $value['pol']; ?>"><?php echo __('read'); ?></a></td></tr>';
		<?php
			}
		?>
		modal.find('#results').html(r);
		showResults();
		modal.find('#ban').off("click").change(function(){
			search_ban = this.checked;
			showResults();
		});
		modal.find('#adb').off("click").change(function(){
			search_adb = this.checked;
			showResults();
		});
		$("tr.item").each(function() {
			var tr = $(this)
			tr.off("click").click(function(e){
				$('#lib').val( tr.attr('h') );
				$('#close').click();
			});
		});		
		$( "a.policy" ).click(function( event ) {
		  event.stopPropagation();
		});
	})
})(jQuery);	
</script>
