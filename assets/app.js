// import scss
import './styles/app.scss';

// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

require('./user');
require('./nav');
require('./serverPreview');
require('./alert');
require('./advanced');
require('./fetchFile');
require('./backup');
require('./createNewServer');
require('./console');
