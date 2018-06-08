<div class="wrap">
	<h2>Order <?php echo $title ?></h2>

	<p id="reorder-loading">
		Drag and Drop to Reorder.
		<span class="hide"><img src="<?php echo $this->imgUrl; ?>/loading.gif" /></span>
	</p>
				
	<div class="reorderSelects">
		<?php
		$taxes = get_object_taxonomies($this->postType);
		if($taxes):
		?>

		<form method="get">
			<input type="hidden" name="page" value="<?php echo $_GET['page']; ?>" />
			<input type="hidden" name="post_type" value="<?php echo $this->postType; ?>" />

			Type

			<select name="reorder_tax" id="reorder_tax">
				<option value=""></option>
				<?php
				foreach($taxes as $name):
					$tax = get_taxonomy($name);
				?>
				<option value="<?php echo $name; ?>" <?php if($this->tax == $name) echo "selected"; ?>><?php echo $tax->label; ?>&nbsp;</option>
				<?php endforeach; ?>
			</select>

			<!-- <input type="submit" name="reorder_tax_submit" value="Submit" class="button" /> -->


			<?php
			if($this->tax and in_array($this->tax, $taxes)):
				$terms = get_terms($this->tax);

				if($terms):
			?>
						
			<select name="reorder_term" id="reorder_terms">
				<option value=""></option>
				<?php foreach($terms as $term): ?>
				<option value="<?php echo $term->slug; ?>" <?php if($this->term == $term->slug) echo "selected"; ?>><?php echo $term->name; ?></option>
				<?php 
					endforeach;
				endif;
				?>


			</select>

			<?php endif; ?>


			<input type="submit" name="reorder_term_submit" value="Submit" class="button" /><br /><br />

		</form>

		<?php endif; ?>
	</div>
				

	<table class="widefat post fixed">
		<thead>
			<tr>
				<th>Title</th>
				<th width="80">Actions</th>
			</tr>
		</thead>	

		<tr>
			<td style="padding: 0" colspan="2" id="reorder-list">

				<ul id="order-posts-list<?php if(is_post_type_hierarchical($this->postType)) echo '-nested'; ?>" class="page-list">
					<?php $this->buildList(); ?>
				</ul>						

			</td>
		</tr>

		<tfoot>
			<tr>
				<th>Title</th>
				<th>Actions</th>
			</tr>
		</tfoot>
	</table>	
</div>