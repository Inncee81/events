/* eslint-env node */

import { resolve } from 'path';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

export default {
  entry: {
    'themes/visita/js/visita.js': './src/themes/visita/visita.js',
    'plugins/visita/js/admin.js': './src/plugins/visita/admin.js',

    'plugins/admin/css/login.css': './src/plugins/admin/login.scss',
    'themes/visita/style.css': './src/themes/visita/style.scss',
    'plugins/visita/css/admin.css': './src/plugins/visita/admin.scss',
    'plugins/visita/css/fields.css': './src/plugins/visita/fields.scss',
    'themes/visita/editor-style.css': './src/themes/visita/editor-style.scss',
  },
  output: {
    filename: '[name]',
    path: resolve('./html/wp-content/'),
  },
  externals: {
    $: 'jQuery',
    jQuery: 'jQuery',
  },
  module: {
    rules: [
      {
        test: /\.js$/,
        use: 'babel-loader',
        exclude: [/html/, /node_modules/]
      },
      {
        test: /\.(ico|jpe?g|png|gif)$/i,
        use: 'file-loader?limit=500&name=img/[name].[ext]&publicPath=/',
      },
      {
        test: /\.(ttf|eot|svg|woff(2)?)$/,
        use: 'file-loader?limit=10000&name=fonts/[name].[ext]&publicPath=/'
      },
      {
        test: /\.scss?$/,
        use: ExtractTextPlugin.extract({
          use: [
            { loader: 'css-loader' },
            { loader: 'resolve-url-loader' },
            {
              loader: 'sass-loader',
              options: {
                includePaths: [
                  './node_modules/foundation-sites/scss',
                  './node_modules/font-awesome',
                  './node_modules/',
                  './sass'
                ]
              }
            }
          ]
        }),
      },
    ]
  },
  resolve: {
    extensions: ['.js', '.scss'],
    modules: [
      resolve('src'),
      'node_modules',
    ],
    alias: {
      'plugins': resolve('./src/plugins'),
      'themes': resolve('./src/template'),
      'font-awesome': resolve('./node_modules/font-awesome'),
      'foundation': resolve('./node_modules/foundation-sites'),
    }
  }
};
