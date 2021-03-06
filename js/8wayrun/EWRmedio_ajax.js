/** @param {jQuery} $ jQuery Object */
!function($, window, document, _undefined)
{

	// *********************************************************************

	XenForo.BulkEditor = function($form)
	{
		var media = $form.data('media');
			
		$form.click(function(e)
		{
			e.preventDefault();
			$('#extra_'+media).slideToggle('slow');
		});
	}
	
	// *********************************************************************
	
	XenForo.SortColumn = function($column)
	{
		$column.sortable({
			connectWith: ".sortColumn",
			cursor: "move",
			distance: 10,
			placeholder: "portlet-placeholder",
			revert: "true",
			tolerance: "pointer",
		});

		$column.disableSelection();
	}
	
	XenForo.SortDelete = function($link)
	{
		$link.click(function(e)
		{
			e.preventDefault();

			$link.parent().xfRemove("xfFadeOut");;
		});
	}

	// *********************************************************************
	
	XenForo.CommentFeedLoader = function($link)
	{
		$link.click(function(e)
		{
			e.preventDefault();

			XenForo.ajax(
				$link.attr('href'),
				{},
				function(ajaxData, textStatus)
				{
					if (XenForo.hasTemplateHtml(ajaxData))
					{
						new XenForo.ExtLoader(ajaxData, function()
						{
							$(ajaxData.templateHtml).xfInsert('replaceAll', '#mediaComments', 'xfShow');
						});
					}
				}
			);
		});
	}

	// *********************************************************************

	XenForo.CommentPoster = function($form)
	{
		$form.bind('AutoValidationBeforeSubmit', function(e)
		{
			XenForo.MultiSubmitFix($form);
		});

		$form.bind('AutoValidationComplete', function(e)
		{
			if (e.ajaxData._redirectMessage)
			{
				$form.find('textarea[name=message]').val('').blur();
				$form.find('input:submit').blur();

				$('#CommentStatus').text(e.ajaxData._redirectMessage);
				setTimeout(function() { $('#CommentStatus').text(''); }, 4000);
			}

			if (XenForo.hasTemplateHtml(e.ajaxData))
			{
				new XenForo.ExtLoader(e.ajaxData, function()
				{
					$(e.ajaxData.templateHtml).xfInsert('replaceAll', '#mediaComments', 'xfShow');
				});
			}
		});
	}

	// *********************************************************************
	
	XenForo.CommentEditor = function($form)
	{
		$form.bind('AutoValidationComplete', function(e)
		{
			if (XenForo.hasTemplateHtml(e.ajaxData))
			{
				new XenForo.ExtLoader(e.ajaxData, function()
				{
					$(e.ajaxData.templateHtml).xfInsert('replaceAll', '#comment_'+$form.data('comment'), 'xfShow');
				});
			}
		});
	}

	// *********************************************************************
	
	XenForo.CommentDeleter = function($form)
	{
		$form.bind('AutoValidationComplete', function(e)
		{
			$('#comment_'+$form.data('comment')).xfRemove("xfFadeOut");
		});
	}

	// *********************************************************************

	XenForo.SlugText = function($text)
	{
		$text.slugIt({
			output: '.SlugOut'
		});
	}

	// *********************************************************************

	XenForo.SlugEdit = function($text)
	{
		$text.slugIt({
			events: 'focus blur',
			output: '.SlugOut'
		});
	}

	// *********************************************************************

	XenForo.KeywordText = function($text)
	{
		$text.slugIt({
			events: 'focus blur',
			output: '.KeywordText',
			type: 'keys'
		});
	}

	// *********************************************************************

	XenForo.KeywordEdit = function($form)
	{
		$form.bind('AutoValidationComplete', function(e)
		{
			if (XenForo.hasTemplateHtml(e.ajaxData))
			{
				new XenForo.ExtLoader(e.ajaxData, function()
				{
					$(e.ajaxData.templateHtml).xfInsert('replaceAll', '#mediaKeywords', 'xfShow');
					$form.find('#keywordAdd').val('');
				});
			}
		});
	}

	// *********************************************************************

	XenForo.UserEdit = function($form)
	{
		$form.bind('AutoValidationComplete', function(e)
		{
			if (XenForo.hasTemplateHtml(e.ajaxData))
			{
				new XenForo.ExtLoader(e.ajaxData, function()
				{
					$(e.ajaxData.templateHtml).xfInsert('replaceAll', '#mediaUsers', 'xfShow');
					$form.find('#userAdd').val('');
				});
			}
		});
	}

	// *********************************************************************

	XenForo.CustomControl = function($link)
	{
		$link.click(function(e)
		{
			$('.customSearch').toggle();
		});
	}

	// *********************************************************************

	XenForo.register('.BulkEditor', 'XenForo.BulkEditor');
	XenForo.register('.sortColumn', 'XenForo.SortColumn');
	XenForo.register('.sortDelete', 'XenForo.SortDelete');
	XenForo.register('#CommentFeed div.PageNav a[href]', 'XenForo.CommentFeedLoader');
	XenForo.register('#CommentPoster', 'XenForo.CommentPoster');
	XenForo.register('#CommentEditor', 'XenForo.CommentEditor');
	XenForo.register('#CommentDeleter', 'XenForo.CommentDeleter');
	XenForo.register('.SlugIn', 'XenForo.SlugText');
	XenForo.register('.SlugEdit', 'XenForo.SlugEdit');
	XenForo.register('.KeywordText', 'XenForo.KeywordText');
	XenForo.register('.KeywordEdit', 'XenForo.KeywordEdit');
	XenForo.register('.UserEdit', 'XenForo.UserEdit');
	XenForo.register('.CustomControl', 'XenForo.CustomControl');
}
(jQuery, this, document);