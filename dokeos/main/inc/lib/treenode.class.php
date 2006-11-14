<?php // $Id: treenode.class.php 7893 2006-03-02 09:54:40Z  $
/*
==============================================================================
	Dokeos - elearning and course management software

	Copyright (c) Bart Mollet <bart.mollet@hogent.be>

	For a full list of contributors, see "credits.txt".
	The full license can be read in "license.txt".

	This program is free software; you can redistribute it and/or
	modify it under the terms of the GNU General Public License
	as published by the Free Software Foundation; either version 2
	of the License, or (at your option) any later version.

	See the GNU General Public License for more details.

	Contact address: Dokeos, 44 rue des palais, B-1030 Brussels, Belgium
	Mail: info@dokeos.com
==============================================================================
*/
/**
==============================================================================
*	@package dokeos.library
==============================================================================
*/

define('TREENODE_ROOT', 0);
/**
 * An object of this class represents a Node in a tree.
 * @author Bart Mollet <bart.mollet@hogent.be>
 */
class TreeNode
{
	/**
	 * The id of this TreeNode
	 */
	var $id;
	/**
	 * The id of the parent TreeNode
	 */
	var $parent_id;
	/**
	 * The left value of the TreeNode
	 */
	var $lft;
	/**
	 * The right value of the TreeNode
	 */
	var $rgt;
	/**
	 * The data stored in this TreeNode
	 */
	var $data;
	/**
	 * The database in which this TreeNode is stored
	 */
	var $db_name;
	/**
	 * Create a TreeNode
	 * @param int $id The id of the TreeNode (or TREENODE_ROOT)
	 */
	function TreeNode($id, $db_name)
	{
		$this->id = $id;
		$this->db_name = $db_name;
		// We do not store the root-node in the database.
		if ($this->id == TREENODE_ROOT)
		{
			$this->parent_id = 0;
			$this->lft = 1;
			$result = $this->debug_sql('SELECT (count(*)+1)*2 AS n FROM '.$this->db_name);
			$count = mysql_fetch_object($result);
			$this->rgt = $count->n;
			$this->data = null;
		}
		// All other nodes come from the database
		else
		{
			$result = $this->debug_sql('SELECT * FROM '.$this->db_name.' WHERE id="'.$this->id.'";');
			$node = mysql_fetch_object($result);
			$this->parent_id = $node->parent;
			$this->lft = $node->lft;
			$this->rgt = $node->rgt;
			unset ($node->parent);
			unset ($node->id);
			unset ($node->lft);
			unset ($node->rgt);
			$this->data = $node;
		}
	}
	/**
	 * Add a child to this TreeNode
	 * @param object $data The data to be stored in this TreeNode
	 * @return TreeNode the new child.
	 */
	function add_child($data)
	{
		$sql1 = 'UPDATE '.$this->db_name.' SET rgt=rgt+2 WHERE rgt>='. ($this->rgt);
		$sql2 = 'UPDATE '.$this->db_name.' SET lft=lft+2 WHERE lft>'. ($this->rgt);
		$sql3 = 'INSERT INTO '.$this->db_name.' SET parent='.$this->id.' ,lft='. ($this->rgt).', rgt='. ($this->rgt + 1);
		foreach(get_object_vars($data) as $name => $value)
		{
			$sql3 .= ", ".$name."='".$value."'"; 	
		}
		if( $this->id == TREENODE_ROOT)
		{
			$this->rgt += 2;
		}
		$this->debug_sql($sql1);
		$this->debug_sql($sql2);
		$this->debug_sql($sql3);
		return mysql_insert_id();
	}
	/**
	 * Delete this TreeNode and all its children
	 */
	function delete()
	{
		$children = $this->get_children();
		foreach ($children as $index => $child)
		{
			$child->delete();
		}
		$this = new TreeNode($this->id,$this->db_name);
		$sql1 = 'DELETE FROM '.$this->db_name.' WHERE id='.$this->id;
		$sql2 = 'UPDATE '.$this->db_name.' SET rgt=rgt-2 WHERE rgt>'. ($this->rgt);
		$sql3 = 'UPDATE '.$this->db_name.' SET lft=lft-2 WHERE lft>'. ($this->lft);
		$this->debug_sql($sql1);
		$this->debug_sql($sql2);
		$this->debug_sql($sql3);
	}
	/** 
	 * Get all children of this node
	 */
	function get_children()
	{
		$res = $this->debug_sql('SELECT id FROM '.$this->db_name.' WHERE parent = '.$this->id);
		$nodes = array ();
		while ($node = mysql_fetch_object($res))
		{
			$nodes[] = $node->id;
		}
		return $nodes;
	}
	/**
	 * Get the path of this node from the root
	 */
	function get_path()
	{
		$res = $this->debug_sql('SELECT id FROM '.$this->db_name.' WHERE lft < '.$this->lft.' AND rgt > '.$this->rgt);
		$nodes = array ();
		while ($node = mysql_fetch_object($res))
		{
			$nodes[] = new TreeNode($node->id,$this->db_name);
		}
		return $nodes;
	}
	/** 
	 * Debug function
	 */
	function debug_sql($sql)
	{
		//echo '<pre>'.$sql.'</pre></br>';
		return mysql_query($sql);	
	}
}
?>