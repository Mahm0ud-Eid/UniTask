<?php

/**
 * Class PermissionType
 * @package common\enum
 */

namespace common\enum;

class PermissionType
{
    #admin
    public const ADMIN = 'admin';
    public const VIEW_STUDENTS = 'canViewStudents';
    public const ADD_STUDENTS = 'canAddStudents';
    public const VIEW_TEACHERS = 'canViewTeachers';
    public const ADD_TEACHERS = 'canAddTeachers';
    public const VIEW_SUBJECTS = 'canViewSubjects';
    public const ADD_SUBJECTS = 'canAddSubjects';
    public const VIEW_DEPARTMENTS = 'canViewDepartments';
    public const ADD_DEPARTMENTS = 'canAddDepartments';
    #teacher
    public const TEACHERS = 'teachers';
    public const MANAGE_SUBJECTS = 'canManageSubjects';
    public const CREATE_QUIZZES = 'canCreateQuizzes';
    public const CREATE_TASKS = 'canCreateTasks';
    public const CREATE_MATERIALS = 'canCreateMaterials';
    #student
    public const STUDENTS = 'students';
    public const ATTEMPT_QUIZZES = 'canAttemptQuizzes';
    public const VIEW_RESULTS = 'canViewResults';
    public const ATTEMPT_TASKS = 'canAttemptTasks';
}
