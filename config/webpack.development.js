/* eslint-env node */

import webpack from 'webpack';
import defaultConfig from './webpack.default';
import HtmlWebpackPlugin from 'html-webpack-plugin';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

export default {
  ...defaultConfig,
  watch: true,
  devtool: 'eval',
  plugins: [
    new ExtractTextPlugin({
      allChunks: true,
      filename: '[name]',
    }),
    new webpack.ProvidePlugin({
      $: 'jquery',
      jQuery: 'jquery',
      'window.jQuery': 'jquery'
    }),
    new HtmlWebpackPlugin({
      inject: false,
      excludeJSChunks: 'style',
    }),
    new webpack.NoEmitOnErrorsPlugin(),
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('development')
      }
    })
  ]
};
