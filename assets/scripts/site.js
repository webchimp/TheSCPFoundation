/**
 * site.js
 * Base logic, feel free to replace with your own and/or use the libraries of your choice
 */
Site = Class.extend({
	init: function(options) {
		var obj = this,
			opts = _.defaults(options, {
				// Add options here
			});
		jQuery(document).ready(function($) {
			obj.onDomReady($);
		});
	},
	scrollToElement: function(el, ms){
		var speed = (ms) ? ms : 600;

		$('html, body').animate({
			scrollTop: $(el).offset().top
		}, speed);
	},
	onDomReady: function($) {
		var obj = this;

		$('.js-toggle-site-navigation').on('click', function(event) {
			event.preventDefault();
			$('body').toggleClass('is-navigation-open');
		});

		$('body').keydown(function(e) {
			if(e.keyCode == 37) { // left

				prevLink = $('.object-prev').attr('href');
				window.location.href = prevLink;

			} else if(e.keyCode == 39) { // right

				nextLink = $('.object-next').attr('href');
				window.location.href = nextLink;
			}
		});

		/*setTimeout(function() {
			var nextLink = $('.object-next').attr('href'),
				scp = $('.object-next').text();

			window.location.href = nextLink;
		}, 100);*/

		$('.js-show-restricted-content').on('click', function(event) {
			event.preventDefault();
			$('.is-restricted').show();
		});

		$('a.footnoteref').tooltipster({
			maxWidth: 400,
			functionInit: function(instance, helper) {

				var id = $(helper.origin).attr('id'),
					content = $('#' + id.replace('ref', '')).text();
				instance.content(content);
			},
			theme: 'tooltipster-borderless'
		});

		$('.footnote-footer a:first-child').on('click', function(event) {
			event.preventDefault();
			var el = $(this),
				footnoteFooter = el.closest('.footnote-footer'),
				reference = footnoteFooter.attr('id');

			console.log('#' + reference.replace('footnote', 'footnoteref'));

			obj.scrollToElement('#' + reference.replace('footnote', 'footnoteref'));
		});

		$('.yui-navset').each(function(index, el) {

			var el = $(this),
				yuiNavSet = el,
				yuiNav = el.find('.yui-nav'),
				yuiContent = el.find('.yui-content');

			yuiNav.find('a').on('click', function(event) {

				event.preventDefault();
				var el = $(this),
					li = el.closest('li'),
					lis = li.siblings('li'),
					index = li.index();

				lis.removeClass('selected');
				li.addClass('selected');

				yuiContent.find('> div').hide();
				yuiContent.find('> div:eq(' + index + ')').show();
			});
		});

		$('.collapsible-block .collapsible-block-link').on('click', function(event) {
			event.preventDefault();
			var el = $(this),
				block = el.closest('.collapsible-block'),
				folded = block.find('.collapsible-block-folded'),
				unfolded = block.find('.collapsible-block-unfolded');

			if(folded.is(':visible')) {

				folded.slideUp();
				unfolded.slideDown();

			} else {

				folded.slideDown();
				unfolded.slideUp();
			}
		});

		$('.site-navigation').on('click', 'a', function(event) {
			event.preventDefault();
			var el = $(this),
				href = el.attr('href');
			$([document.documentElement, document.body]).animate({
				scrollTop: $(href).offset().top - 70
			}, 1500);
		});

		$('body').on('mouseenter', '[data-scp-tooltip]:not(.tooltipstered)', function(){

			var el = $(this),
				side = el.data('side') || ['top', 'bottom', 'right', 'left'],
				maxWidth = el.data('max-width') || 500;

			el.tooltipster({
				content: 'Loading...',
				interactive: true,
				side: side,
				maxWidth: maxWidth,
				functionBefore: function(instance, helper) {

					var origin = $(helper.origin),
						scp = origin.data('scp-tooltip');

					if (origin.data('loaded') !== true) {

						$.get(constants.siteUrl + 'helper/tooltip/' + scp, function(data) {

							data = $(data);

							instance.content(data);
							origin.data('loaded', true);
						});
					}
				},
				theme: 'tooltipster-shadow'
			}).tooltipster('open');
		});

		$('.scp-special-containment-procedures [data-navigation]').each(function(index, el) {
			var el = $(this),
				title = el.data('navigation'),
				href = el.attr('id');

				$('.navigation-special-containment-procedures').append('<li class="menu-item"><a href="#' + href + '">' + title + '</a></li>');
		});

		$('.scp-description [data-navigation]').each(function(index, el) {
			var el = $(this),
				title = el.data('navigation'),
				href = el.attr('id');
				$('.navigation-description').append('<li class="menu-item"><a href="#' + href + '">' + title + '</a></li>');
		});

		$('body').on('click', '.js-open-search', function(event) {
			event.preventDefault();
			$('.overlay-close').addClass('js-close-search');
			$('.overlay-close, .search').fadeIn();
			$('.search #search-query').focus();
		});

		$('body').on('click', '.js-close-search', function(event) {
			event.preventDefault();
			$('.overlay-close').removeClass('js-close-search');
			$('.overlay-close, .search').fadeOut();
		});

		$('body').on('keyup', '#search-query', function(event) {
			var el = $(this),
				query = el.val();

			$.ajax({
				url: constants.siteUrl + 'search/' + query,
				dataType: 'json',
				success: function(response) {

					var searchResult = _.template($('#search-result').html());
					$('.search-results').html('');

					_.each(response.data.search_results, function(result) {
						$('.search-results').append(searchResult({ object: result }));
					});
				}
			});
		});

		if($('body').hasClass('page-home')) {

			$('.warning-title').velocity('transition.shrinkIn', { delay: 500 });
			$('.warning-classified').velocity('transition.shrinkIn', { delay: 1000 });
			$('.warning-subtitle').velocity('transition.shrinkIn', { delay: 1500 });
			$('.warning-cta').velocity('transition.shrinkIn', { delay: 3000 });

			$('.warning-cta .button').on('click', function(event) {
				event.preventDefault();
				$('.warning').velocity('transition.shrinkOut', { complete: function(){
					$('.warning').addClass('hide');
					$('.login').removeClass('hide');

					$('.login-logo').velocity('transition.shrinkIn', { delay: 500 });
					$('.login-title').velocity('transition.shrinkIn', { delay: 1000 });
					$('.login-subtitle').velocity('transition.shrinkIn', { delay: 1500 });
					$('.login-credentials').velocity('transition.shrinkIn', { delay: 2000 });
				} });
			});

			$('.login-credentials').on('submit', function(event) {
				var el = $(this),
					password = el.find('input').val();

				if(!password) {
					el.velocity('callout.shake');
					return false;
				} else {
					return true;
				}
			});
		}

		$('.glitch').mgGlitch({
			destroy : false,
			glitch: true,
			scale: true,
			blend : true,
			blendModeType : 'hue',
			glitch1TimeMin : 2000,
			glitch1TimeMax : 4000,
			glitch2TimeMin : 100,
			glitch2TimeMax : 1000,
		});
	}
});

var site = new Site();