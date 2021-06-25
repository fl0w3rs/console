# Console

Веб-система CAD/MDT (Computer Aided Dispatch / Mobile Data Terminal) для ролевого игрового сервера: рабочее место диспетчера, бортовой компьютер патрульных юнитов и база данных гражданских персонажей. Сделана в 2020 году для проекта Southland, работала в продакшене на живом сообществе.

![MDT](docs/screenshots/02-mdt.png)

## Скриншоты

| | |
|---|---|
| ![Рабочий стол](docs/screenshots/01-dashboard.jpg) Рабочий стол с выбором департамента | ![CAD](docs/screenshots/06-cad.png) Диспетчерская (CAD) |
| ![NCIC](docs/screenshots/03-ncic.png) Поиск по базе NCIC | ![Ордера](docs/screenshots/04-warrants.png) Ордера |
| ![Дело](docs/screenshots/05-casefile.png) Дело (case file) | ![Civil](docs/screenshots/07-civil.png) Гражданская часть |

## Возможности

- **Диспетчерская (CAD)** — вызовы и ситуации, прикрепление юнитов к вызовам, статусы юнитов в реальном времени, кнопки паники, приоритета и Signal 100.
- **MDT** — поиск по базам NCIC/DMV, ориентировки (BOLO), штрафы, аресты, ордера с подписью и исполнением, дела (case files) с комментариями, бланки рапортов.
- **Гражданская часть** — персонажи с фото, медкартой, транспортом и оружием, оплата штрафов.
- **Админка** — управление департаментами и игровой зоной (AOP).
- **Интеграция с игрой** — сервер может опрашивать API, чтобы узнать текущую зону и включён ли Signal 100.
- **Авторизация через форум** — вход по сессии форума IPS4, права зависят от группы пользователя.
- **Realtime** — обновления через WebSocket-сервер: PHP-бэкенд шлёт события, клиенты получают их без перезагрузки.

## Стек

- **Веб-часть:** PHP 7, MySQL ([RedBeanPHP](https://redbeanphp.com/)), [Twig](https://twig.symfony.com/), jQuery, MDBootstrap.
- **Realtime-сервер:** Node.js, [ws](https://github.com/websockets/ws), Sequelize.

## Структура

```
console.sql            схема базы данных
web/                   веб-часть (PHP)
  index.php            маршруты
  system/
    config.php         настройки (БД, форум, WebSocket)
    api/               JSON-эндпоинты: персонажи, штрафы, ордера, дела…
    views/             Twig-шаблоны и обработчики страниц
    libraries/         роутер, RedBeanPHP, WebSocket-клиент
  assets/              стили и клиентский JS
websocket/             realtime-сервер (Node.js)
  main.js              обработчики событий: вызовы, юниты, ориентировки…
  db.js                модели Sequelize
```

## Запуск

1. Импортировать `console.sql` в MySQL.
2. **WebSocket-сервер:** в `websocket/` скопировать `.env.example` в `.env` и заполнить его, затем выполнить `yarn install` и `yarn start`.
3. **Веб-часть:** заполнить `web/system/config.php`: доступы к базам CAD и форума IPS4, `dir_prefix`, `forum_root` и параметры WebSocket (`ws_host`, `ws_port`, `ws_ssl` для PHP, `ws_public_url` для браузера, `ws_server_token` — тот же, что `SERVER_TOKEN` в `.env`). Затем в `web/` выполнить `composer install`.
4. Отдавать `web/` через Apache с `mod_rewrite`. `RewriteBase` в `.htaccess` должен совпадать с `dir_prefix`.
