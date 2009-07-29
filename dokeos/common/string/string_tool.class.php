<?php
class StringTool 
{
    
	/**
	 * Tests if a string starts with a given string
	 * 
	 * @param string $string
	 * @param string $start
	 * @return bool
	 */
    public static function start_with($string, $start)
    {
        return strpos($string, $start) === 0;
    }
    
	/**
     * Tests if a string ends with the given string
     *
     * @param string
     * @param string
     * @return bool
     */
    function end_with($string, $end)
    {
        return strrpos($string, $end) === strlen($string) - strlen($end);
    }
    
	/**
     * Return the string found between two characters. 
     * 
     * If an index is given, it returns the value at the index position.
     * e.g. $index = 3 --> returns the value between the third occurence of $opening_char and $closing_char
     * 
     * @param string $opening_char
     * @param string $closing_char
     * @param int $index 0 based index
     * @return string or null
     */
    public static function get_value_between_chars($haystack, $index = 0, $opening_char = '[', $closing_char = ']')
    {
        $offset = 0;
        $found = true;
        $value = null;
        
        for ($i = 0; $i < $index + 1; $i++)
        {
            $op_pos = strpos($haystack, $opening_char, $offset);
            if($op_pos !== false)
            {
                $cl_pos = strpos($haystack, $closing_char, $op_pos + 1);
    
                if($cl_pos !== false)
                {
                    $value = substr($haystack, $op_pos + 1, $cl_pos - $op_pos - 1);
                    $offset = $cl_pos + 1;
                }
                else
                {
                    $found = false;
                    break;
                }
            }
            else
            {
                $found = false;
                break;
            }
        }
        
        if($found)
        {
            return $value;
        }
        else
        {
            return null;
        }
    }
    
}
?>