( function($)
    {
        var handle_charttype = function(ev, ui)
        {
            var parent = $(this).parent().parent().parent();
            var block = parent.attr('id');
            var type = $(this).val();
            var pare = $('.reporting_content', parent);

            pare.html(getLoadingBox('ChangingDisplaymode'));
            $.post("./reporting/ajax/reporting_change_charttype.php?"+gup(),
            {
                block:  block,
                type: type
            },	function(data)
            {
                if(data.length > 0)
                {
                    pare.html(data);
                }
            }
            );
		
            return false;
        }

        function getLoadingBox(message)
        {
            var loadingHTML  = '<div align="center" class="loadingBox">';
            loadingHTML += '<div class="loadingMedium" style="margin-bottom: 15px;">';
            loadingHTML += '</div>';
            loadingHTML += '<div>';
            loadingHTML += translation(message, 'reporting');
            loadingHTML += '</div>';
            loadingHTML += '</div>';

            return loadingHTML;
        }

        function translation(string, application) {
            var translated_string = $.ajax({
                type: "POST",
                url: "./common/javascript/ajax/translation.php",
                data: {
                    string: string, application: application
                },
                async: false
            }).responseText;

            return translated_string;
        }

        function gup()
        {
            var query=this.location.search.substring(1);
            var params2 = "";
            if (query.length > 0)
            {
                var params=query.split("&");
                for (var i=0 ; i<params.length ; i++)
                {
                    var pos = params[i].indexOf("=");
                    var name = params[i].substring(0, pos);
                    var value = params[i].substring(pos + 1);
                    var template_parameter = name.indexOf("template_parameters");
                    if(template_parameter > -1)
                    {
                        name = name.replace('%5D',']');
                        name = name.replace('%5B','[');
                        params2 += name+'=';
                        params2 += value+'&';
                    }
                }
            } // -->
            return params2;
        }
	
        $(document).ready( function()
        {
            $(".charttype").bind('change',handle_charttype);
        });
    })(jQuery);