あるプロジェクトを立ち上げたい、ユーザーが加盟店のお気に入り登録ができたりクーポンを取得できたりできるアプリ、加盟店は名前やカテゴリ検索が可能で取得しているクーポン、お気に入り加盟店の一覧が表示される

バックエンド：laravel11,php8.2
フロント:nuxt.js,vue.js3,javascript
環境:docker


はい、素晴らしいプロジェクトのアイデアですね。Laravel 11とNuxt.js（Vue.js 3）を使用し、Dockerで環境を構築するプロジェクトを立ち上げる方法を説明します。

1. プロジェクトのセットアップ


Dockerを使用して、LaravelとNuxt.jsの環境を一緒にセットアップします。以下のような構造でプロジェクトを作成します：

```
project-root/
├── backend/
├── frontend/
└── docker-compose.yml
```

まず、プロジェクトのルートディレクトリを作成し、そこに移動します：

```
mkdir my-shop-app && cd my-shop-app
```

2. Dockerの設定


`docker-compose.yml`ファイルを作成し、以下の内容を記述します：

```
services:
  frontend:
    build: ./frontend
    environment:
      - NODE_ENV=development
      - HOST=0.0.0.0
      - CHOKIDAR_USEPOLLING=true
    volumes:
      - ./frontend:/app
    command: sh -c 'npm install && npm run dev'
    ports:
      - '3000:3000'
    tty: true
  
  backend:
    build:
      context: ./backend
      : 
    volumes:
      - ./backend:/var/www/html
    ports:
      - "8000:80"
    depends_on:
      - mysql
    environment:
      - APP_KEY=${APP_KEY}
      - DB_CONNECTION=mysql
      - DB_HOST=mysql
      - DB_PORT=3306
      - DB_DATABASE=${DB_DATABASE}
      - DB_USERNAME=${DB_USERNAME}
      - DB_PASSWORD=${DB_PASSWORD}

  mysql:
    image: mysql:8.0
    command: --default-authentication-plugin=mysql_native_password
    ports:
      - '3306:3306'
    environment:
      MYSQL_ROOT_PASSWORD: ${MYSQL_ROOT_PASSWORD}
      MYSQL_DATABASE: ${MYSQL_DATABASE}
      MYSQL_USER: ${MYSQL_USER}
      MYSQL_PASSWORD: ${MYSQL_PASSWORD}
    volumes:
      - mysql_data:/var/lib/mysql
      
  phpmyadmin:
    image: phpmyadmin/phpmyadmin
    ports:
      - "8080:80"
    environment:
      PMA_HOST: mysql
      PMA_USER: root
      PMA_PASSWORD: ${MYSQL_ROOT_PASSWORD}

volumes:
  mysql_data:
```

3. バックエンド（Laravel）のセットアップ


バックエンドディレクトリを作成し、Laravelプロジェクトを初期化します：

```
mkdir backend && cd backend
docker run --rm -v $(pwd):/app composer create-project --prefer-dist laravel/laravel:^11.0 .
```

`backend/`を作成し、以下の内容を記述します：

```
FROM php:8.2-apache

# PHPの拡張機能をインストール
RUN docker-php-ext-install pdo pdo_mysql

# Apacheの設定を変更
RUN a2enmod rewrite

# Composerをインストール
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

# 権限の設定
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage

# Apacheのドキュメントルートを変更
ENV APACHE_DOCUMENT_ROOT /var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

CMD ["apache2-foreground"]

RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
```

4. フロントエンド（Nuxt.js）のセットアップ


フロントエンドディレクトリを作成し、Nuxt.jsプロジェクトを初期化します：

```
cd nuxt-laravel
npx nuxi@latest init frontend
// or
docker exec npx nuxi@latest init frontend
```

プロジェクト作成時の質問には、以下のように答えてください：

- Nuxt.jsのバージョン: 最新の3.x
- JavaScriptを選択
- パッケージマネージャー: npm
- UIフレームワーク: Tailwind CSS（推奨）
- その他の機能は必要に応じて選択


`frontend/Dockerfile`を作成し、以下の内容を記述します：

```
FROM node:14

WORKDIR /app

COPY package*.json ./

RUN npm install

COPY . .

RUN npm run build

EXPOSE 3000

CMD [ "npm", "run", "start" ]
```

5. プロジェクトの起動


プロジェクトのルートディレクトリに戻り、以下のコマンドでDockerコンテナを起動します：

```
docker-compose up -d
```

これで、`http://localhost:8000`でLaravelバックエンド、`http://localhost:3000`でNuxt.jsフロントエンドにアクセスできます。
