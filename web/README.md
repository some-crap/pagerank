Перед запуском программы необходимо настроить веб-сервер с php7 и python3.

Для этого можно арендовать VPS на CentOS7.

Для того, чтобы не настраивать вручную можно установить бесплатную панель управления сервером VestaCP.

После установки ПУ необходимо инсталировать Python3.

Открываем соединение по SSH из под root пользователя и вводим следущие команды:


curl -O http://vestacp.com/pub/vst-install.sh


bash vst-install.sh


Дальше вводим необходимые данные и на всё соглашаемся.


yum install https://centos7.iuscommunity.org/ius-release.rpm

yum install python36u python36u-devel python36u-pip


Далее привязываем доменное имя к аккаунту и ip-адресу и загружаем все файлы в корень сайта.

Работающий пример: https://pagerank.somecrap.ru
