<?php #builder.php

		ob_start();
		
		if (isset($_GET["lp"]))
		{
					$_GET["lp"] = "false"; 		
		
		}
		else
		{
			if (isset($_REQUEST["lp"]))
			{
						$_REQUEST["lp"] = "false";
			}
		}

		$in = 1;
		
		$integrationValue = 0; // addresource
		
		include GetLocalFileName($_SERVER["PHP_SELF"]);
		
		// you must edit the HTML content 
		$content = ob_get_clean();
		
		// rewrite the content
		echo GetHeader($content)." ".CheckForms(ReWriteUrls(SelectContent($content)));
		//echo SelectContent($content);
		//echo "hehe ". ($content)."... :)";

		ob_end_flush();		
		exit();
		
	  
	  // functions section
	  function GetHeader($contents)
	  {
	  		$head = "";
				$contents = strtolower($contents);
				$pattern = array ( 1 => "head>", 2 => "/head>");
				$s_contents = substr($contents,0,strpos($contents,$pattern["2"])-1);
				$e_contents = substr($s_contents,strpos($contents,$pattern["1"])+strlen($pattern["1"]),strlen($s_contents));
				$head = $e_contents;
				
				return $head;
  			
	  }
	  
	  function GetLocalFileName($fname)
  	{
  		$name = explode('/',$fname);
  		$name = $name[sizeof($name)-1];
  		return $name;  		
  	}

		
		// Cut the Header and the Footer
		function SelectContent($content)
		{			 				
				
				$pre = strstr($content,"<!-- end of the whole #header section -->");
				$post = strstr($pre," <!-- end of #main\" started at the end of claro_init_banner.inc.php -->");
				$content = substr($pre,0,strlen($pre)-strlen($post));				
				return $content;				
				//return strlen($pre).":".strlen($post);				
		}

		// ReWriteUrls
		function ReWriteUrls($content)
		{
			
			$go = 0;
			$f= 0;			
			$s = 0;
			$destContent = "";
			$pre = 0;
			$tmp = 0;
			$post = 0;
			

			for($i=0;$i<strlen($content)-2;$i++)
			{								
				
				if(strcmp(substr($content,$i,2),"<a")==0)
				{
					$go = 1;					
					$l = 1;
				}				
				if ($go)
				{
					if ($f)
					{
						
						if (strcmp(substr($content,$i,1),"?")==0)
						{
							$s = 1;	
						}
						if (strcmp(substr($content,$i,1),"#")==0)
						{
							$s = 1;	
						}
						
						if (strcmp(substr($content,$i,1),"\"")==0)
						{
								$f = 0;
							$go =0;
							$l = 0;
							$pre = $tmp;
							$post = $i;
							$destContent .= substr($content,$pre,$post-$pre);
							
							if ($s)
								{
								$destContent = $destContent . "&lp=true";							 	
								}
							else	
								{
								$destContent = $destContent . "?lp=true";							 	
								}
							$tmp = $post;								
							$s = 0;
							
						}
					}
					else
					{
						if (strcmp(substr($content,$i,1),"\"")==0)
						{
							$l = 0;
							$f = 1;							
						}
					}
				}
								if ($go)
				{
					if ($f)
					{
						
						if (strcmp(substr($content,$i,1),"?")==0)
						{
							$s = 1;	
						}
						if (strcmp(substr($content,$i,1),"#")==0)
						{
							$s = 1;	
						}
						
						if (strcmp(substr($content,$i,1),"'")==0)
						{
								$f = 0;
							$go =0;
							$l = 0;
							$pre = $tmp;
							$post = $i;
							$destContent .= substr($content,$pre,$post-$pre);
							
							if ($s)
								{
								$destContent = $destContent . "&lp=true";							 	
								}
							else	
								{
								$destContent = $destContent . "?lp=true";							 	
								}
							$tmp = $post;								
							$s = 0;
							
						}
					}
					else
					{
						if (strcmp(substr($content,$i,1),"'")==0)
						{
							$l = 0;
							$f = 1;							
						}
					}
				}
				if ($l)
				{				
						if (strcmp(substr($content,$i,1),"?")==0)
						{
							$s = 1;	
						}
						if (strcmp(substr($content,$i,1),"#")==0)
						{
							$s = 1;	
						}	
					if (strcmp(substr($content,$i,1),">")==0)
					{	
						if($go)
							{
								$go = 0;
								$l = 0;
								$pre = $tmp;
								$post = $i;
								$destContent .= substr($content,$pre,$post-$pre);
								if ($s)
									{
									$destContent = $destContent . "&lp=true";							 	
									}
								else	
									{
									$destContent = $destContent . "?lp=true";							 	
									}						 	
								$s = 0;
								$tmp = $post;								
							}
					}						
				}				
			}		
			$footer = substr($content,$tmp,strlen($content)-$tmp);
			
			return $destContent.$footer;
			//return $footer;
		}

	function CheckForms($content)
	{
		$field = ' <input type="hidden" name="lp" value="true"> ';						
		$tmpc = strtolower($content);
		$matches = array();
		preg_match_all("'<form[^>]*>'si",$tmpc,$matches, PREG_SET_ORDER);
		if (count($matches)>0)
		{
			while(list($key,$match) = each($matches))
			{				
				while(list($value,$str)=each($match))
					{
								$index = strpos($tmpc,$str);
								$index += strlen($str);
								$destContent = substr($content,0,$index);
								$destContent .= $field;
								$destContent .= substr($content,$index,strlen($content)-$index);
					}
			}
		}
		else
		{
			$destContent = $content;
		}
		return $destContent;	
	}
?>