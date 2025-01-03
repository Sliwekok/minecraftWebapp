// import scss
import './styles/app.scss';

// create global $ and jQuery variables
const $ = require('jquery');
global.$ = global.jQuery = $;

require('./alert');
require('./nav');
require('./user');
require('./serverPreview');
require('./advanced');
require('./fetchFile');
require('./backup');
require('./createNewServer');
require('./console');
require('./players');
require('./mods');
require('./modsPaginator');
require('./tos');
