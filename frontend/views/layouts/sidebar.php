<?php

use kartik\icons\Icon;
use yii\bootstrap5\Html;
use yii\helpers\Url;

// check if is_admin is true or false
$isAdmin = Yii::$app->user->identity->is_admin;
$isTeacher = Yii::$app->user->identity->is_teacher;

?>
<aside style="background-color: white" class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="#" class="brand-link">
        <img src="/img/btu-logo-en.png" alt="Logo" class="brand-image  elevation-3" style="opacity: .9">
        <span class="sd-text brand-text font-weight-normal text-center text-wrap">BTU</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="<?= 'https://www.gravatar.com/avatar/' . md5(Yii::$app->user->identity->email) ?>"
                    class=" elevation-2" alt="User Image" width="160" height="160">
            </div>
            <div class="info">
                <?php
                echo Html::a(
                    ($isTeacher ? 'Dr/ ' : '') . Yii::$app->user->identity->name,
                    null,
                    ['class' => 'd-block']
                );
                ?>
            </div>
        </div>
        <!-- SidebarSearch Form -->
        <!-- href be escaped -->
        <!-- <div class="form-inline">
            <div class="input-group" data-widget="sidebar-search">
                <input class="form-control form-control-sidebar" type="search" placeholder="Search" aria-label="Search">
                <div class="input-group-append">
                    <button class="btn btn-sidebar">
                        <i class="fas fa-search fa-fw"></i>
                    </button>
                </div>
            </div>
        </div> -->

        <!-- Sidebar Menu -->
        <nav class="mt-2 sd-text side-cont">
            <?php
            if (!$isAdmin && !$isTeacher) {
                echo \hail812\adminlte\widgets\Menu::widget([
                    'items' => [
                        [
                            'label' => 'Quizzes',
                            'icon' => 'tasks',
                            'iconStyle' => 'fa-duotone',
                            'items' => [
                                [
                                    'label' => 'View',
                                    'url' => ['/quizzes'],
                                    'active' => Yii::$app->controller->id === 'quizzes' &&
                                    (Yii::$app->controller->action->id === 'index' || Yii::$app->controller->action->id === 'start') ? 'active' : '',
                                    'icon' => 'tasks'
                                ],
                                [
                                    'label' => 'Results',
                                    'iconStyle' => 'far',
                                    'url' => ['/quizzes/results'],
                                    'active' => Yii::$app->controller->action->id === 'result-index' ? 'active' : '',
                                    'icon' => 'square-poll-vertical'
                                ],
                            ]
                        ],
                        [
                            'label' => 'Tasks',
                            'icon' => 'code',
                            'active' => Yii::$app->controller->id === 'tasks' ? 'active' : '',
                            'items' => [
                                [
                                    'label' => 'View',
                                    'url' => ['/tasks'],
                                    'active' => Yii::$app->controller->id === 'tasks' &&
                                    (Yii::$app->controller->action->id === 'index' || Yii::$app->controller->action->id === 'view')
                                    ? 'active' : '',
                                    'icon' => 'code'
                                ],
                                [
                                    'label' => 'Results',
                                    'url' => ['/tasks/results'],
                                    'active' => Yii::$app->controller->action->id === 'results' ? 'active' : '',
                                    'icon' => 'square-poll-vertical',
                                    'iconStyle' => 'fa-duotone',
                                ],
                            ]
                        ],
                        [
                            'label' => 'Study Materials',
                            'url' => ['/materials'],
                            'icon' => 'books',
                            'iconStyle' => 'fa-duotone',
                            'active' => Yii::$app->controller->id === 'materials' ? 'active' : '',
                        ]
                    ],
                ]);
            } elseif ($isTeacher) {
                echo \hail812\adminlte\widgets\Menu::widget([
                    'items' => [
                        [
                            'label' => 'Quizzes',
                            'url' => ['/teacher/quizzes'],
                            'icon' => 'tasks',
                            'iconStyle' => 'fa-duotone',
                            'active' => Yii::$app->controller->id === 'quizzes' ? 'active' : '',
                        ],
                        [
                            'label' => 'Tasks',
                            'url' => ['/teacher/tasks'],
                            'icon' => 'tasks',
                            'iconStyle' => 'fa-duotone',
                            'active' => Yii::$app->controller->id === 'tasks' ? 'active' : '',
                        ],
                        [
                            'label' => 'Materials',
                            'url' => ['/teacher/materials'],
                            'icon' => 'files',
                            'iconStyle' => 'fas',
                            'active' => Yii::$app->controller->id === 'materials' ? 'active' : '',
                        ]
                    ]
                ]);
            } elseif ($isAdmin) {
                echo \hail812\adminlte\widgets\Menu::widget([
                    'items' => [
                        [
                            'label' => 'Admin',
                            'icon' => 'cog',
                            'items' => [
                                [
                                    'label' => 'Students',
                                    'iconStyle' => 'far',
                                    'url' => ['/admin/students'],
                                    'active' => Yii::$app->controller->id === 'students' ? 'active' : '',
                                    'icon' => 'user'
                                ],
                                [
                                    'label' => 'Teachers',
                                    'url' => ['/admin/teachers'],
                                    'active' => Yii::$app->controller->id === 'teachers' ? 'active' : '',
                                    'icon' => 'chalkboard-teacher'
                                ],
                                [
                                    'label' => 'Admins',
                                    'url' => ['/admin/admins'],
                                    'active' => Yii::$app->controller->id === 'admins' ? 'active' : '',
                                    'icon' => 'user-shield'
                                ],
                                [
                                    'label' => 'Departments',
                                    'url' => ['/admin/departments'],
                                    'active' => Yii::$app->controller->id === 'departments' ? 'active' : '',
                                    'icon' => 'building'
                                ],
                                [
                                    'label' => 'Subjects',
                                    'url' => ['/admin/subjects'],
                                    'active' => Yii::$app->controller->id === 'subjects' ? 'active' : '',
                                    'icon' => 'book'
                                ],
                                [
                                    'label' => 'Semesters',
                                    'url' => ['/admin/semesters'],
                                    'active' => Yii::$app->controller->id === 'semesters' ? 'active' : '',
                                    'icon' => 'calendar-alt'
                                ],
                            ],
                        ],
                    ]
                ]);
            }
            ?>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>