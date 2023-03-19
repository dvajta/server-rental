<p align="center">
    <a href="https://github.com/yiisoft" target="_blank">
        <img src="https://avatars0.githubusercontent.com/u/993323" height="100px">
    </a>
    <h1 align="center">Token-based authentication application in Yii2</h1>
    <br>
</p>

To start the project, you need to clone the git repository git@github.com:dvajta/server-rental.git. After cloning the project, you need to:

1. Run the command `composer install` in the console to install all project dependencies from the `composer.json` file.
2. Create a database for the project in MySql and specify the connection in the project config file `common/config/main-local.php`.
3. Configure the `Nginx` server as shown in the example.
4. Run migrations by executing the command `php yii migrate` while in the project's root directory.

After completing all the steps, 2 tables will be created in the database:
1. The `user` table, where 5 user profiles will be created using seeding.
2. The `user_json_data` table where the Json data for the current task will be recorded.

To check the operation of authentication and recording Json data, you need to:
1. Run the console command to generate an access token `php yii cron/get-token --login=pkrajcik --password=secret123`. For all users during data seeding, the same password `secret123` was generated. 
To find out the login, you need to go to the user table and take the username from any record in the list.
2. After executing the console command, a token will be generated and written to the user table. The token's lifetime for user authentication is 5 minutes. Then we go to the main page of the application where we see two forms. 
One for adding Json data, and the other for updating. We insert the token into the form, select the data submission method, and insert the Json string we want to add. 
If we want to update the record, then in the second form, we also select the `ID` of the entry and insert the instruction code shown as an example in the input field.
3. To access the admin panel, you need to type `https:\\mydomain\admin`. After that, you will enter the admin panel and be able to see the list of saved data with the ability to view and delete each element.

 Задача    | Оценка | Затрачено | Комментарий |
|---------|----------|----------|-------------|
| Настройка окружения | 1 час | 2 часа | Решил настроить на Nginx (ранее не настраивал) |
| Установка фреймворка | 15 мин | 15 мин | 
| Скрипт №1 | 30 мин | 1 час | 
| Скрипт №2 | 30 мин | 30 мин | большая часть логики уже была описана в 1-м скрипте.
| Скрипт №3 | 40 мин | 2 часа | Не сразу понял что от меня требуется 
| Скрипт №4 | 30 мин | 1.5 часа | Не сразу понял, что список нужно чтоб форммировался из любого объекта
| Тесты | 1 час | 3 часа | Не писал тесты, нужно было разбираться.

Ссылка на проект: https://jemadar.ru/



Nginx config example
-------------------

```
# server configuration
server {
    listen 80;
    server_name example.com;

    set $base_root /path/to/project/root;
    root $base_root;

    #error_log /var/log/nginx/advanced.local.error.log warn;
    #access_log /var/log/nginx/advanced.local.access.log main;
    charset UTF-8;
    index index.php index.html;

    # remove trailing slash
    location ~ .+/$ {
        rewrite ^/(.+)/$ /$1 permanent;
    }

    location / {
        root $base_root/frontend/web;
        try_files $uri $uri/ /frontend/web/index.php$is_args$args;

        # omit static files logging, and if they don't exist, avoid processing by Yii (uncomment if necessary)
        location ~ ^/.+\.(css|js|ico|png|jpe?g|gif|svg|ttf|mp4|mov|swf|pdf|zip|rar)$ {
            log_not_found off;
            access_log off;
            try_files $uri =404;
        }

        location ~ ^/assets/.+\.php(/|$) {
            deny all;
        }
    }

    location /admin {
        alias $base_root/backend/web/;

        # prevent the directory redirect to the URL with a trailing slash
        location = /admin {
            try_files $uri /backend/web/index.php$is_args$args;
        }

        try_files $uri $uri/ /backend/web/index.php$is_args$args;

        # omit static files logging, and if they don't exist, avoid processing by Yii (uncomment if necessary)
        location ~ ^/admin/.+\.(css|js|ico|png|jpe?g|gif|svg|ttf|mp4|mov|swf|pdf|zip|rar)$ {
            log_not_found off;
            access_log off;
            try_files $uri =404;
        }

        location ~ ^/admin/assets/.+\.php(/|$) {
            deny all;
        }
    }

    location ~ ^/.+\.php(/|$) {
        rewrite (?!^/((frontend|backend)/web|admin))^ /frontend/web$uri break;
        rewrite (?!^/backend/web)^/admin(/.+)$ /backend/web$1 break;

        #fastcgi_pass 127.0.0.1:9000; # proxy requests to a TCP socket
        fastcgi_pass unix:/run/php/php7.4-fpm.sock; # proxy requests to a UNIX domain socket (check your www.conf file)
        fastcgi_split_path_info ^(.+\.php)(.*)$;
        include /etc/nginx/fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        try_files $fastcgi_script_name =404;
    }

    location ~ /\. {
        deny all;
    }
}
```
