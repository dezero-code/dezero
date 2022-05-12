<?php
/*
|--------------------------------------------------------------------------
| SIDEBAR (menu) partial
|--------------------------------------------------------------------------
|
| Available variables:
|  - $this: dezero\web\View component
|
*/
?>
<div class="site-menubar">
  <?=
    $this->render('//layouts/_sidebar_menu', [
      'vec_items' => [

        // Categories
        [
          'label' => 'Categorías',
          'url'   => '#',
          'icon'  => 'wb-tag',
          'is_active' => ($current_module == 'bike' || ($current_module == 'commerce' && $current_controller == 'size')),
          'visible' => Yii::$app->user->can('category_manage'),
          'items' => [
            [
              'label' => 'Tipos',
              'url'   => ['/category/bike'],
            ],
            [
              'label' => 'Tallas',
              'url'   => ['/commerce/size'],
            ],
            [
              'label' => 'Extras',
              'url'   => ['/commerce/extra'],
            ],
          ],
        ],

        // Customer
        [
          'label' => 'Clientes',
          'url'   => '#',
          'icon'  => 'wb-user-circle',
          'is_active' => ($current_module == 'commerce' && $current_controller == 'customer'),
          'visible' => Yii::$app->user->can('customer_manage'),
          'items' => [
            [
              'label' => 'Ver todos',
              'url'   => ['/commerce/customer', 'Customer[status_filter]' => 'active'],
            ],
          ],
        ],

        // Pages and texts
        [
          'label' => 'Páginas & contenido',
          'url'   => '#',
          'icon'  => 'wb-file',
          'is_active' => ( ($current_module == 'web' && $current_controller != 'contact') || ( $current_module == 'config' && $current_controller == 'translate' ) ),
          // 'visible' => 'FALSE',
          'visible' => Yii::$app->user->can('web_manage'),
          'items' => [
            [
              'label' => 'Home page',
              'url'   => ['/web/block/home'],
            ],
            [
              'label' => 'About page',
              'url'   => ['/web/block/about'],
            ],
            [
              'label' => 'Contact page',
              'url'   => ['/web/block/contact'],
            ],
            [
              'label' => 'Order finished page',
              'url'   => ['/web/block/finished'],
            ],
            [
              'label' => 'FAQ\'s',
              'url'   => ['/web/faq'],
            ],
            [
              'label' => 'Legal pages',
              'url'   => ['/web/legal'],
            ],
            // [
            // 'label' => 'Footer',
            // 'url'   => ['/web/block/footer'],
            // ],
          ],
        ],

        // Users & permissions
        [
          'label' => 'Usuarios & permisos',
          'url'   => '#',
          'icon'  => 'wb-users',
          'is_active' => ($current_module == 'user'),
          'visible' => Yii::$app->user->can('user_manage'),
          'items' => [
            [
              'label' => 'Listado usuarios',
              'url'   => ['/user/admin'],
            ],
            [
              'label' => 'Roles & Permisos',
              'url'   => ['/auth/dzAuth/tasks'],
            ],
            [
              'label' => 'Roles (avanzado)',
              'url'   => ['/auth/role'],
            ],
            [
              'label' => 'Permisos (avanzado)',
              'url'   => ['/auth/task'],
            ],
          ],
        ],

        // Configuration
        [
          'label' => 'Configuración',
          'url'   => '#',
          'icon'  => 'wb-settings',
          'is_active' => ($current_module == 'settings'),
          'visible' => Yii::$app->user->can('settings_manage'),
          'items' => [
            [
              'label' => 'Traducciones',
              'url'   => ['/settings/translation', 'category' => 'newhorizon'],
            ],
            [
              'label' => 'Commerce',
              'url'   => ['/settings/commerce'],
            ],
            [
              'label' => 'Idiomas',
              'url'   => ['/settings/language'],
            ],
            [
              'label' => 'Monedas',
              'url'   => ['/settings/currency'],
            ],
            /*
            [
              'label' => 'Plantillas Email',
              'url'   => ['/config/mailTemplate'],
            ],
            */
            [
              'label' => 'Países',
              'url'   => ['/settings/country']
            ],
          ],
        ],

        // Administration
        [
          'label' => 'Administración',
          'url'   => '#',
          'icon'  => 'wb-wrench',
          'is_active' => ($current_module == 'admin'),
          // 'visible' => Yii::$app->user->isAdmin(),
          'items' => [
            [
              'label' => 'Información Sistema',
              'url'   => ['/admin/info'],
            ],
            [
              'label' => 'Información PHP',
              'url'   => ['/admin/php'],
            ],
            [
              'label' => 'Ficheros Logs',
              'url'   => ['/admin/log'],
            ],
            [
              'label' => 'Emails Enviados',
              'url'   => ['/admin/mail'],
            ],
            [
              'label' => 'Database Backups',
              'url'   => ['/admin/backup'],
            ],
            [
              'label' => 'PhpMyAdmin',
              // 'url'   => App::phpMyAdmin(),
              'url' => 'fail',
              'htmlOptions' => [
                'target' => '_blank'
              ]
            ],
          ],
        ],

        // Mails
        [
          'label' => 'Mails & Contacto',
          'url'   => '#',
          'icon'  => 'wb-envelope',
          'is_active' => ( ($current_module == 'web' && $current_controller == 'contact') || ( $current_module == 'admin' && $current_controller == 'mail' ) ),
          'visible' => Yii::$app->user->can('config_manage'),
          'items' => [
            [
              'label' => 'Contacto - Mensajes',
              'url'   => ['/web/contact'],
            ],
            [
              'label' => 'Mails - Enviados',
              'url'   => ['/admin/mail'],
            ],
            // [
            //  'label' => 'Mails - Pendientes',
            //  'url'   => ['/config/mailHistory', 'MailHistory[is_pending]' => 1],
            // ],
          ]
        ],

        // Logout
        [
          'label' => 'Cerrar sesión',
          'url'   => ['/user/logout'],
          'icon'  => 'wb-power'
        ],
      ]
    ]);
  ?>
</div>
