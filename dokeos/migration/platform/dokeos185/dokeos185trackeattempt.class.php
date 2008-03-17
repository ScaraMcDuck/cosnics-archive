<?php 
/**
 * migration.lib.platform.dokeos185
 */

/**
 * This class presents a dokeos185 track_e_attempt
 *
 * @author Sven Vanpoucke
 */
class Dokeos185TrackEAttempt
{
	/**
	 * Dokeos185TrackEAttempt properties
	 */
	const PROPERTY_EXE_ID = 'exe_id';
	const PROPERTY_USER_ID = 'user_id';
	const PROPERTY_QUESTION_ID = 'question_id';
	const PROPERTY_ANSWER = 'answer';
	const PROPERTY_TEACHER_COMMENT = 'teacher_comment';
	const PROPERTY_MARKS = 'marks';
	const PROPERTY_COURSE_CODE = 'course_code';
	const PROPERTY_POSITION = 'position';
	const PROPERTY_TMS = 'tms';

	/**
	 * Default properties stored in an associative array.
	 */
	private $defaultProperties;

	/**
	 * Creates a new Dokeos185TrackEAttempt object
	 * @param array $defaultProperties The default properties
	 */
	function Dokeos185TrackEAttempt($defaultProperties = array ())
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Gets a default property by name.
	 * @param string $name The name of the property.
	 */
	function get_default_property($name)
	{
		return $this->defaultProperties[$name];
	}

	/**
	 * Gets the default properties
	 * @return array An associative array containing the properties.
	 */
	function get_default_properties()
	{
		return $this->defaultProperties;
	}

	/**
	 * Get the default properties
	 * @return array The property names.
	 */
	static function get_default_property_names()
	{
		return array (SELF :: PROPERTY_EXE_ID, SELF :: PROPERTY_USER_ID, SELF :: PROPERTY_QUESTION_ID, SELF :: PROPERTY_ANSWER, SELF :: PROPERTY_TEACHER_COMMENT, SELF :: PROPERTY_MARKS, SELF :: PROPERTY_COURSE_CODE, SELF :: PROPERTY_POSITION, SELF :: PROPERTY_TMS);
	}

	/**
	 * Sets a default property by name.
	 * @param string $name The name of the property.
	 * @param mixed $value The new value for the property.
	 */
	function set_default_property($name, $value)
	{
		$this->defaultProperties[$name] = $value;
	}

	/**
	 * Sets the default properties of this class
	 */
	function set_default_properties($defaultProperties)
	{
		$this->defaultProperties = $defaultProperties;
	}

	/**
	 * Returns the exe_id of this Dokeos185TrackEAttempt.
	 * @return the exe_id.
	 */
	function get_exe_id()
	{
		return $this->get_default_property(self :: PROPERTY_EXE_ID);
	}

	/**
	 * Sets the exe_id of this Dokeos185TrackEAttempt.
	 * @param exe_id
	 */
	function set_exe_id($exe_id)
	{
		$this->set_default_property(self :: PROPERTY_EXE_ID, $exe_id);
	}
	/**
	 * Returns the user_id of this Dokeos185TrackEAttempt.
	 * @return the user_id.
	 */
	function get_user_id()
	{
		return $this->get_default_property(self :: PROPERTY_USER_ID);
	}

	/**
	 * Sets the user_id of this Dokeos185TrackEAttempt.
	 * @param user_id
	 */
	function set_user_id($user_id)
	{
		$this->set_default_property(self :: PROPERTY_USER_ID, $user_id);
	}
	/**
	 * Returns the question_id of this Dokeos185TrackEAttempt.
	 * @return the question_id.
	 */
	function get_question_id()
	{
		return $this->get_default_property(self :: PROPERTY_QUESTION_ID);
	}

	/**
	 * Sets the question_id of this Dokeos185TrackEAttempt.
	 * @param question_id
	 */
	function set_question_id($question_id)
	{
		$this->set_default_property(self :: PROPERTY_QUESTION_ID, $question_id);
	}
	/**
	 * Returns the answer of this Dokeos185TrackEAttempt.
	 * @return the answer.
	 */
	function get_answer()
	{
		return $this->get_default_property(self :: PROPERTY_ANSWER);
	}

	/**
	 * Sets the answer of this Dokeos185TrackEAttempt.
	 * @param answer
	 */
	function set_answer($answer)
	{
		$this->set_default_property(self :: PROPERTY_ANSWER, $answer);
	}
	/**
	 * Returns the teacher_comment of this Dokeos185TrackEAttempt.
	 * @return the teacher_comment.
	 */
	function get_teacher_comment()
	{
		return $this->get_default_property(self :: PROPERTY_TEACHER_COMMENT);
	}

	/**
	 * Sets the teacher_comment of this Dokeos185TrackEAttempt.
	 * @param teacher_comment
	 */
	function set_teacher_comment($teacher_comment)
	{
		$this->set_default_property(self :: PROPERTY_TEACHER_COMMENT, $teacher_comment);
	}
	/**
	 * Returns the marks of this Dokeos185TrackEAttempt.
	 * @return the marks.
	 */
	function get_marks()
	{
		return $this->get_default_property(self :: PROPERTY_MARKS);
	}

	/**
	 * Sets the marks of this Dokeos185TrackEAttempt.
	 * @param marks
	 */
	function set_marks($marks)
	{
		$this->set_default_property(self :: PROPERTY_MARKS, $marks);
	}
	/**
	 * Returns the course_code of this Dokeos185TrackEAttempt.
	 * @return the course_code.
	 */
	function get_course_code()
	{
		return $this->get_default_property(self :: PROPERTY_COURSE_CODE);
	}

	/**
	 * Sets the course_code of this Dokeos185TrackEAttempt.
	 * @param course_code
	 */
	function set_course_code($course_code)
	{
		$this->set_default_property(self :: PROPERTY_COURSE_CODE, $course_code);
	}
	/**
	 * Returns the position of this Dokeos185TrackEAttempt.
	 * @return the position.
	 */
	function get_position()
	{
		return $this->get_default_property(self :: PROPERTY_POSITION);
	}

	/**
	 * Sets the position of this Dokeos185TrackEAttempt.
	 * @param position
	 */
	function set_position($position)
	{
		$this->set_default_property(self :: PROPERTY_POSITION, $position);
	}
	/**
	 * Returns the tms of this Dokeos185TrackEAttempt.
	 * @return the tms.
	 */
	function get_tms()
	{
		return $this->get_default_property(self :: PROPERTY_TMS);
	}

	/**
	 * Sets the tms of this Dokeos185TrackEAttempt.
	 * @param tms
	 */
	function set_tms($tms)
	{
		$this->set_default_property(self :: PROPERTY_TMS, $tms);
	}

}

?>