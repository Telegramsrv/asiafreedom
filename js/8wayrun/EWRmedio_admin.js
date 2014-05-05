/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{
	// *********************************************************************
	
	XenForo.SplashAdder = function($button)
	{
		$button.click(function(e)
		{
			var $source = $($button.data('source')), $clone = null;

			if ($source.length)
			{
				var $counter = $source.last().data('counter');
				
				$clone = $source.last().clone();
				$clone.attr('data-counter', $counter+1);
				$clone.find('[name]').attr('name', function (_, name) {
					return name.replace('splash]['+$counter, 'splash]['+($counter+1))
				});
				$clone.find('[id]').attr('id', function (_, id) {
					return id.replace('splash'+$counter, 'splash'+($counter+1))
				});
				
				$clone.find('input[type="button"]').remove();
				$clone.find('input:not([type="button"], [type="submit"])').val('');
				$clone.find('option:selected').prop('selected', false);
				
				$clone.xfInsert('insertAfter', $source.last(), false, false, function()
				{
					var $inputs = $clone.find('input');
					$inputs.prop('disabled', false);
					$inputs.first().focus().select();
				})
			}
		});
	};
	
	// *********************************************************************

	XenForo.register('.SplashAdder', 'XenForo.SplashAdder');
}
(jQuery, this, document);