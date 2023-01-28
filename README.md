# Framework

## Installation

### Basic instructions

- `git clone git@bitbucket.org:dzukis/exchange.git`
  `cp .env.dist .env`
- Edit `.env` and configure it for your 

### Laravel Mix

[Laravel Mix](https://laravel.com/docs/7.x/mix) provides a fluent API for defining Webpack build steps for your Laravel application using several common CSS and JavaScript pre-processors.

Laravel Mix supports:
- SASS
- LESS
- Stylus
- ES2015 syntax and modules (using __import__ and __require__)
- `.vue` single file components


- change directory to `assets`
- `npm install`

Source files are in `assets/js` and `assets/css`. Builts files are in `assets/dist`.

Inside __sass__ you can import `.css` from node modules using the __~__ character, ex:
`@import "~jquery.filer/css/jquery.filer.css";`

### Using Laravel Mix

Running `npm run watch` will build a development version with sourcemaps and development version of Vue.

Running `npm run prod` will build a production version without sourcemaps and with production version of Vue.

There is `npm run hot` but that doesn't work in our setup.

