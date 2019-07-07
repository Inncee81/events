/* eslint-env node */

import { resolve } from 'path';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

export default {
  entry: {
    'themes/visita/js/visita.js': './src/themes/visita/visita.js',
    'themes/vidalatina/js/visita.js': './src/themes/vidalatina/vidalatina.js',
    'plugins/visita/js/admin.js': './src/plugins/visita/admin.js',

    'themes/visita/login.css': './src/themes/visita/login.scss',
    'themes/vidalatina/login.css': './src/themes/vidalatina/login.scss',
    'themes/visita/editor-style.css': './src/themes/common/editor-style.scss',
    'themes/vidalatina/editor-style.css': './src/themes/common/editor-style.scss',
    'themes/visita/inline.css': './src/themes/visita/inline.scss',
    'themes/vidalatina/inline.css': './src/themes/vidalatina/inline.scss',
    'themes/visita/tablet.css': './src/themes/common/tablet.scss',
    'themes/vidalatina/tablet.css': './src/themes/common/tablet.scss',
    'themes/visita/style.css': './src/themes/visita/style.scss',
    'themes/vidalatina/style.css': './src/themes/vidalatina/style.scss',

    'plugins/visita/css/admin.css': './src/plugins/visita/admin.scss',
    'plugins/visita/css/fields.css': './src/plugins/visita/fields.scss',
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
        exclude: [/html/, /node_modules\/!(foundation)/]
      },
      {
        test: /\.(ico|jpe?g|png|gif)$/i,
        use: 'file-loader?limit=500&name=themes/visita/img/[name].[ext]&publicPath=/wp-content/',
      },
      {
        test: /\.scss?$/,
        use: ExtractTextPlugin.extract({
          use: [
            { loader: 'css-loader',
              options: {
                sourceMap: true,
                url: false,
              }
            },
            {
              loader: 'sass-loader',
              options: {
                sourceMap: true,
                includePaths: [
                  './node_modules/tiny-date-picker/',
                  './node_modules/foundation-sites/scss',
                  './node_modules/font-awesome/',
                  './node_modules/',
                  './sass'
                ]
              }
            }
          ]
        }),
      },
      {
        test: /\.css$/,
        use: 'postcss-loader'
      }
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
      'font-awesome': resolve('./node_modules/font-awesome/'),
      'foundation': resolve('./node_modules/foundation-sites/')
    }
  }
};
