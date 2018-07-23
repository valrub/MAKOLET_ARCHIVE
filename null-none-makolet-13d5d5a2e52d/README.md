# Приложение makolet #

* Рабочая область для работы с проектом
* Статические библиотеки
* Настройки по приложению ionicframework
* * Конфигурация платформ
* * Тестирование
* * Запуск на десктопе
* * Тестирования на реальном устройстве
* Локализация проекта
* Запуск emulator
* Генерация .apk файл

## Рабочая область для работы с проектом ##

1. установка [git](https://git-scm.com/downloads) на локальный компьютер
2. установка [gui](https://www.sourcetreeapp.com/) для работы с кодом (или пользоваться с консоли командами)
3. после всех установок можно сделать clone проекта https://null-none@bitbucket.org/null-none/makolet.git и с помощью gui редактора добавлять код (например перевод текста)


## Статические библиотеки ##

Все настройки статических библиотек хранятся в корневом файле bower.json. После установки код сохарняется в папке makolet/app/www/lib/ чтобы добавить новую библиотеку, нужно добавить название библиотеки и версию и выполнить команду:

```
#!bash

bower install
```

```
#!json

{
  "name": "Makolet",
  "private": "true",
  "devDependencies": {
    "ionic": "driftyco/ionic-bower#1.2.4"
  },
  "dependencies": {
    "ngCordova": "^0.1.24-alpha",
    "angular-translate": "^2.9.1",
    "angular-translate-loader-static-files": "^2.9.1",
    "angular-input-masks": "^2.1.1",
    "angular-google-maps": "^2.3.2",
    "ngstorage": "^0.3.10",
    "ionic-rating": "^0.3.0",
    "angular-resource": "^1.5.0",
    "angular-cookie": "^4.1.0",
    "ionic-timepicker": "^0.4.0"
  }
}

```


## Настройки по приложению ionicframework ##

### Конфигурация платформ ###

```
#!bash

ionic platform add ios
ionic platform add android
```

### Тестирование ###

```
#!bash

ionic build ios
ionic emulate ios

ionic build android
ionic emulate android
```

### Запуск на десктопе ###

```
#!bash

ionic serve

```

ссылка в браузере http://0.0.0.0:8100/ionic-lab

### Тестирования на реальном устройстве ###

```
#!bash

ionic run android -l -c -s
ionic run ios -l -c -s

```

## Локализация проекта ##

Путь к файлу, где лежит локализация: makolet/app/www/languages/


Формат локализации на иврите il.json:
```
#!json

{
  "EMAIL": "אֶלֶקטרוֹנִי דואר",
  "PASSWORD": "סִיסמָה",
...

```

Формат локализации на английском en.json:

```
#!json

{
  "EMAIL": "Email",
  "PASSWORD": "Password",
...

```

Первый параметр константа, которая лежит в основе всех шаблонов, второй параметр это текст перевода. Если надо перевести, берем текст на иврите копируем его, заходим в файл el.json поиском находим его и изменяем.


## Запуск emulator ##

```bash

ionic emulate ios --livereload --consolelogs --serverlogs
ionic emulate android --livereload --consolelogs --serverlogs

ionic run ios -l -c -s
ionic run android -l -c -s

```


## Генерация .apk файла ##

```bash

sudo cordova build --release android

sudo jarsigner -verbose -sigalg SHA1withRSA -digestalg SHA1 -keystore my-release-key.keystore android-release-unsigned.apk alias_name

/PATH/zipalign -v 4 android-release-unsigned.apk /PATH/android.apk

```
