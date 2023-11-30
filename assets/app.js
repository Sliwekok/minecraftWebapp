// import scss
import './styles/app.scss';

// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

require('./user');
require('./createNew');
require('./serverPreview');
require('./alert');
require('./advanced');