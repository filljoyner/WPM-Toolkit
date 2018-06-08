jQuery(document).ready(function($) {
	
var config = { 
	accept: 'page-item',
	noNestingClass: "no-nesting",
	opacity: 0.5,
	helperclass: 'reorder-highlight',
	autoScroll: true,
	onChange: function(serialized) {
		$('#reorder-loading span').show();	
		$.ajax({
			type: "POST",
			url: ajaxurl,
			data: { 
				sort:serialized[0].o,
				action:'wpm_reorder',
			},
			success: function() {
				$('#reorder-loading span').hide();
			}
		})
	}
};

$(function() {
	$(".show-hide-toggle").click(function(){
		var id = $(this).attr('id');
		$('#reorder-loading span').show();
		$.post(ajaxurl, {
			id:id,
			action:'wpm_reorder_toggle',
		}, function(data) {
			$('#postDisplay-' + id).html(data);
			$('#reorder-loading span').hide();
		});
		return false;
	});
});

$('#order-posts-list-nested').NestedSortable(config);
// $('#order-posts-list').Sortable(config);
});