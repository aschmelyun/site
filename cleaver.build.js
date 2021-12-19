let AfterWebpack = require('on-build-webpack2');

module.exports = {
    cleaver: new AfterWebpack(() => {
        require('node-cmd').run('php cleaver build', (error, stdout, stderr) => {
            console.log(error ? stderr : stdout);
        });
    })
}