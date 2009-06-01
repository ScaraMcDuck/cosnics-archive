(function($){
	$.fn.extend({ 
		//plugin name - animatemenu
		tabula: function(options) {

			//Settings list and the default values
			var defaults = {
					tabsPerPage: 0,
					nextButton: '',
					prevButton: '',
					follow: false,
					cycle: false
			};
			
			var opts = $.extend(defaults, options);
			
			var self = this, initialized = false, currentPage, 
			buttonWidth, containerWidth, allTabsWidth, tabWidths, 
			maxPageWidth, pages, resizeTimer = null, 
			windowHeight = $(window).height(), windowWidth = $(window).width(),
			currentTab = 0;
			
			function init()
			{
				self.lis = $('ul', self);
				
				allTabsWidth = 0, currentPage = 0, maxPageWidth = 0, buttonWidth = 0,
				pages = new Array(), tabWidths = new Array(), selectedTabWidths = new Array();
			
				containerWidth = $(self).width();
				
				// create next button			
				$li = $('<ul><li></li></ul>')
					.addClass('menu-tabs-next')
				//$a = $('<a href="#"></a>')
					.click(function() { page('next'); return false; })
					.html(opts.nextButton);
				//$li.append($a);
				
				self.lis.eq($(self.lis).length-1).after($li);
				buttonWidth = $li.outerWidth({ margin: true });
				
				// create prev button
				$li = $('<ul><li></li></ul>')
					.addClass('menu-tabs-prev')
				//$a = $('<a href="#"></a>')
					.click(function() { page('prev'); return false; })
					.html(opts.prevButton);
				//$li.append($a);
				self.lis.eq(0).before($li);
				buttonWidth += $li.outerWidth({ margin: true });
				buttonWidth += 8;
				
				// loops through LIs, get width of each tab when selected and unselected.
				self.lis.each(function(i) {
					var listElement = $("li:first", this);
					tabWidths[i] = $(listElement).outerWidth({ margin: true });
					selectedTabWidths[i] = $(listElement).outerWidth({ margin: true });
					allTabsWidth += selectedTabWidths[i];
					
					if($(listElement).hasClass("current"))
					{
						currentTab = i;
					}
				});
				
				// if the width of all tables is greater than the container's width, calculate the pages
				if (allTabsWidth > containerWidth) {
					var pageIndex = 0, pageWidth = 0, maxTabPadding = 0;
					
					// start calculating pageWidths
					for (var i = 0; i < tabWidths.length; i++) {
						// if first tab of page or selected tab's padding larger than the current max, set the maxTabPadding
						if (pageWidth == 0 || selectedTabWidths[i] - tabWidths[i] > maxTabPadding)
							maxTabPadding = (selectedTabWidths[i] - tabWidths[i]);
						
						// if first tab of page, initialize pages variable for page 
						if (pages[pageIndex] == null) {
							pages[pageIndex] = { start: i };
						
						} else if ((i > 0 && (i % opts.tabsPerPage) == 0) || (tabWidths[i] + pageWidth + buttonWidth + 12) > containerWidth) {
							if ((pageWidth + maxTabPadding) > maxPageWidth)	
								maxPageWidth = (pageWidth + maxTabPadding);
							pageIndex++;
							pages[pageIndex] = { start: i };			
							pageWidth = 0;
						}
						pages[pageIndex].end = i+1;
						pageWidth += tabWidths[i];
						if (i == currentTab) currentPage = pageIndex;
					}
					if ((pageWidth + maxTabPadding) > maxPageWidth)	
						maxPageWidth = (pageWidth + maxTabPadding);
	
				    // hide all tabs then show tabs for current page
					self.lis.hide().slice(pages[currentPage].start, pages[currentPage].end).show();
					if (currentPage == (pages.length - 1) && !opts.cycle) 
						disableButton('next');			
					if (currentPage == 0 && !opts.cycle) 
						disableButton('prev');
					
					// calculate the right padding for the next button
					buttonPadding = containerWidth - maxPageWidth - buttonWidth - 8;
					//if (buttonPadding > 0) 
					//	$('.ui-tabs-paging-next', self.element).css({ paddingRight: buttonPadding + 'px' });
					
					initialized = true;
				} else {
					destroy();
				}
				
				$(window).bind('resize', handleResize);
			}
			
			function page(direction) {					
				currentPage = currentPage + (direction == 'prev'?-1:1);
				
				if (direction == 'prev' && currentPage < 0 && opts.cycle)
					currentPage = pages.length - 1;
				else if ((direction == 'prev' && currentPage < 0) || 
				         (direction == 'next' && currentPage >= pages.length))
					currentPage = 0;
				
				var start = pages[currentPage].start;
				var end = pages[currentPage].end;
				self.lis.hide().slice(pages[currentPage].start, pages[currentPage].end).show();
				
				if (direction == 'prev') {
					enableButton('next');
					if (opts.follow && (currentTab < start || currentTab > (end-1))) self.select(end-1);
					if (!opts.cycle && start <= 0) disableButton('prev');
				} else {
					enableButton('prev');
					if (opts.follow && (currentTab < start || currentTab > (end-1))) self.select(start);
					if (!opts.cycle && end >= $(self.lis).length) disableButton('next');
				}
			}
			
			function disableButton(direction) {
				//$('.menu-tabs-'+direction, self.lis).hide();
			}
			
			function enableButton(direction) {
				$('.menu-tabs-'+direction, self.lis).show();
			}
			
			// special function defined to handle IE6 and IE7 resize issues
			function handleResize() {
				if (resizeTimer) clearTimeout(resizeTimer);
				
				if (windowHeight != $(window).height() || windowWidth != $(window).width()) 
					resizeTimer = setTimeout(reinit, 100);
			}
			
			function reinit() {	
				windowHeight = $(window).height();
				windowWidth = $(window).width();
				destroy();
				init();
			}
			
			function destroy() {	
				// remove buttons
				$('.menu-tabs-next', self).remove();
				$('.menu-tabs-prev', self).remove();
				
				// show all tabs
				self.lis.show();
				
				initialized = false;

				$(window).unbind('resize', handleResize);
			}
			
			init();
    	}
	});
})(jQuery);