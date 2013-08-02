<?php global $nonce ?>
<script type="text/javascript">
	function DropDown(el) {
		this.dd = el;
		this.initEvents();
	}
	DropDown.prototype = {
		initEvents : function() {
			var obj = this;

			obj.dd.on('click', function(event){
				$(this).toggleClass('active');
				event.stopPropagation();
			});	
		}
	}
	$(function() {

		var dd = new DropDown( $('#state-prov') );
		var dd = new DropDown( $('#fan-type') );

		$(document).click(function() {
			// all dropdowns
			$('.wrapper-dropdown').removeClass('active');
		});

	});
	$(document).ready(function() {
		$('#profile input#age').spinner({
			min: 1,
			max: 150
		});
		
		$("input[type=file]").filestyle({
			image: "<?php bloginfo('template_directory') ?>/images/file-input.png",
			imagewidth: 103,
			imageheight: 42,
			width: 100
		});
		
		<?php if(is_front_page()) : ?>
			//Load default on page load
			loyal_filter_ajax($('#state-prov .label'), 'CLEAR', 'CLEAR', '<?php echo $nonce ?>');
		<?php endif; ?>
		
		function refresh_toggles() {
			$('.has-hidden-data ~ .hidden-until-checked').hide();
			$('.has-hidden-data:checked ~ .hidden-until-checked').show();
		}
		refresh_toggles();
		
		$('.has-hidden-data').change(function(){
			refresh_toggles();
		});
	});
</script>
<?php wp_footer(); ?>
</body>
</html>