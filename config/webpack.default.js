/* eslint-env node */

import { resolve } from 'path';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

export default {
  entry: {
    //'bundle.js': './src/index.js',
    'themes/visita/js/visita.js': './src/themes/visita/visita.js',

    'plugins/admin/css/admin.css': './src/plugins/admin/admin.scss',
    'plugins/admin/css/login.css': './src/plugins/admin/login.scss',
    'themes/visita/style.css': './src/themes/visita/style.scss',
    'themes/visita/editor-style.css': './src/themes/visita/editor-style.scss',
  },
  output: {
    filename: '[name]',
    path: resolve('./html/wp-content/'),
  },
  externals: {
    jquery: 'jquery'
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
                style: 'compressed',
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
      'static': resolve('./src/static'),
      'plugins': resolve('./src/plugins'),
      'themes': resolve('./src/template'),
      'foundation': resolve('./node_modules/foundation-sites'),
      'font-awesome': resolve('./node_modules/font-awesome'),
    }
  }
};
