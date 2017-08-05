/* eslint-env node */

import webpack from 'webpack';
import defaultConfig from './webpack.default';
import HtmlWebpackPlugin from 'html-webpack-plugin';
import ExtractTextPlugin from 'extract-text-webpack-plugin';

export default {
  ...defaultConfig,
  devtool: 'source-map',
  plugins: [
    new ExtractTextPlugin({
      allChunks: true,
      filename: '[name]',
    }),
    new webpack.ProvidePlugin({
      $: 'jQuery',
      jQuery: 'jQuery',
    }),
    new HtmlWebpackPlugin({
      inject: false,
      excludeJSChunks: 'style',
    }),
    new webpack.optimize.UglifyJsPlugin({
      output: {comments: false},
      compress: {warnings: false}
    }),
    new webpack.DefinePlugin({
      'process.env': {
        NODE_ENV: JSON.stringify('production')
      }
    })
  ]
};
