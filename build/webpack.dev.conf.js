const webpack = require('webpack')
const merge = require('webpack-merge')
const baseWebpackConfig = require('./webpack.base.conf')

const devWebpackConfig = merge(baseWebpackConfig, {
    mode: 'development',
    devtool: 'cheap-module-eval-source-map',
    devServer: {
        contentBase: baseWebpackConfig.externals.paths.dist,
        host: baseWebpackConfig.externals.host,
        port: 8081,
        overlay: {
            warnings: true,
            errors: true
        },
        proxy: {
            '/php/**': {
                target: `http://${baseWebpackConfig.externals.host}:80`,
            }
        }
    },
    output: {
        publicPath: '/',
    },
    plugins: [
        new webpack.SourceMapDevToolPlugin({
            filename: '[file].map'
        })
    ]
})

module.exports = new Promise ((resolve, reject) => {
    resolve(devWebpackConfig)
})
