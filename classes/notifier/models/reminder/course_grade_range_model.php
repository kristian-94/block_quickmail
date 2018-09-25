<?php

namespace block_quickmail\notifier\models\reminder;

use block_quickmail\notifier\models\interfaces\reminder_notification_model_interface;
use block_quickmail\notifier\models\reminder_notification_model;

class course_grade_range_model extends reminder_notification_model implements reminder_notification_model_interface {

    public static $object_type = 'course';

    public static $condition_keys = [
        'grade_greater_than',
        'grade_less_than',
    ];

    /**
     * Returns an array of user ids to be notified based on this reminder_notification_model's conditions
     *
     * @return array
     */
    public function get_user_ids_to_notify()
    {
        // get distinct user ids
        // where users are in a specific course
        // and where have not accessed the course since a conditionally set increment of time before now

        global $DB;

        $query_results = $DB->get_records_sql("SELECT u.id
            FROM {user} u
            INNER JOIN {user_enrolments} ue ON ue.userid = u.id
            INNER JOIN {enrol} e ON e.id = ue.enrolid
            INNER JOIN {course} c ON c.id = e.courseid
            INNER JOIN {role_assignments} ra ON ra.userid = u.id
            INNER JOIN {context} ctx ON ctx.id = ra.contextid AND ctx.instanceid = c.id
            WHERE ra.roleid IN (SELECT CAST(value AS INT) FROM {config} WHERE name='gradebookroles')
            AND c.id = ?
            GROUP BY u.id", [$this->get_course_id()]);

        $course_user_ids = array_keys($query_results);

        // set a default return container
        $results = [];

        foreach ($course_user_ids as $user_id) {
            // fetch "round" grade for this course user
            $round_grade = grade_calculator::get_user_grade_in_course($this->get_course_id(), $user_id, 'round');

        return array_keys($results);
    }

}