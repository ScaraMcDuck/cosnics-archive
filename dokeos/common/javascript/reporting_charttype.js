( function($)
    {
        var handle_charttype = function(ev, ui)
        {
            var parent = $(this).parent().parent().parent();
            var block = parent.attr('id');
            var type = $(this).val();
            parent = $('.reporting_content', parent);

            //var para = serialize_array(params);

            parent.html(getLoadingBox('ChangingDisplaymode'));
            $.post("./reporting/ajax/reporting_change_charttype.php?"+gup(),
            {
                //para: para,
                block:  block,
                type: type
            },	function(data)
            {
                if(data.length > 0)
                {
                    parent.html(data);
                }
            }
            );
		
            return false;
        }

        function serialize_array(array)
        {
            var str = '';

            for(var i in array)
            {
                str += i + '=>' + array[i] + ',';
            }

            str = str.substr(0, str.length - 1);

            return str;

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