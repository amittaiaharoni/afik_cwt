<?
	if (!emptY($this->view_data)){
		?>
		<div>
			
		<?
			$current_page = 1;
			if (!empty($_GET['page_num']) && (int)$_GET['page_num'] > 1)
				$current_page = (int)$_GET['page_num'];
			else
				$_GET['page_num'] = $current_page;
			$qs = "";
			$query_string = $_GET;
			unset($query_string['page_num']);
			$qs = http_build_query($query_string);
			
			
			$starting_numbers_html = "";
			$ending_numbers_html = "";
			$middle_numbers_html = "";
			
			$show_next_back = true;
			
			$show_dots_on_left = false;
			$show_dots_on_right = false;
			$display_from_start = 5;
			$display_from_end = 5;
			$display_in_middle = 9;
			
			$display_middle_on_one_side = (round($display_in_middle,0,PHP_ROUND_HALF_ODD) - 1) / 2;
			// TODO put here BACK
			if ( $show_next_back && !empty($_GET['page_num']) && $_GET['page_num'] !=1 ) {
			?>
				<div style="display: inline-block;" class="pagn_left">
					<a href="index.php?<?=$qs?>&page_num=<?=$_GET['page_num']==1?'1':$_GET['page_num']-1?>">
					  <i class="fa fa-angle-left"></i>
					</a>
				</div>
			<?
			}
			ob_start();
			for ($i = 1; $i <= (int)$this->view_data; $i++){
				
				if ($i > $display_from_start && empty($starting_numbers_html)){
					$starting_numbers_html = ob_get_clean();
					ob_start();
				}
				else if ($current_page + $display_middle_on_one_side > $display_from_start && $i > ($current_page + $display_middle_on_one_side) && empty($middle_numbers_html)){
					$middle_numbers_html = ob_get_clean();
					ob_start();
				}
				if ($i > $display_from_start && $i < ($current_page - $display_middle_on_one_side)){
					$show_dots_on_left = true;
					continue;
				}
				if ($i < (int)$this->view_data - ($display_from_end - 1) && $i > ($current_page + $display_middle_on_one_side)){
					$show_dots_on_right = true;
					continue;
				}
				// if (
						// ($i > $display_from_start && $i < ($current_page - $display_middle_on_one_side)) ||
						// ($i < (int)$this->view_data - ($display_from_end - 1) && $i > ($current_page + $display_middle_on_one_side))
				// ){
					// continue;
				// }
				if (!empty($_GET['page_num'])) {
					if ( !($_GET['page_num'] == $i) ) {
						?>
						<div style="display: inline-block;">
							<a href="index.php?<?=$qs?>&page_num=<?=$i?>">
								<?=$i?>
							</a>
						</div>
						<?
					}
					else {										// Don't show link to current page
						?>
						<div style="display: inline-block; color: #ff7ad5;">
							
								<?=$i?>
							
						</div>
						<?
					}
				}
			}
			if ( $show_next_back && !($_GET['page_num'] == $this->view_data) ) {
			?>
				<div style="display: inline-block;" class="pagn_right">
					<a href="index.php?<?=$qs?>&page_num=<?=$_GET['page_num']>=$this->view_data?$_GET['page_num']:$_GET['page_num']+1?>">
					  <i class="fa fa-angle-right"></i>
					</a>
				</div>
			<?
			}
			$ending_numbers_html = ob_get_clean();
			
			echo $middle_numbers_html;
			echo $starting_numbers_html;
			if ($show_dots_on_left)
				echo '<div style="display: inline-block;">...</div>';
			echo $middle_numbers_html;
			if ($show_dots_on_right)
				echo '<div style="display: inline-block;">...</div>';
			echo $ending_numbers_html;	
		?>
		</div>		
		<?
	}
?>